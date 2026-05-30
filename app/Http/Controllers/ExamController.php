<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Models\QuestionPackage;
use App\Models\QuestionResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ExamController extends Controller {
    /**
     * Tampilkan semua paket soal yang aktif/published untuk dikerjakan siswa.
     */
    public function index(Request $request) {
        $user = Auth::user();
        $packageQuery = QuestionPackage::published()
            ->with(['user', 'classrooms'])
            ->withCount('activeQuestions')
            ->latest();

        // Jika user adalah student, filter berdasarkan kelas
        if ($user->isStudent()) {
            $packageQuery->whereHas('classrooms', function($q) use ($user) {
                $q->whereIn('classrooms.id', $user->classrooms->pluck('id'));
            });
        }

        // Search by name
        if ($request->filled('q')) {
            $packageQuery->where('name', 'like', '%' . $request->q . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $packageQuery->where('package_type', $request->type);
        }

        $packages = $packageQuery->paginate(9, ['*'], 'packages_page')->withQueryString();

        // Cek dan proses auto-submit ujian yang ditinggalkan & sudah kedaluwarsa
        $activeAttempts = QuestionAttempt::where('user_id', Auth::id())
            ->where('is_completed', false)
            ->get();
            
        foreach ($activeAttempts as $attempt) {
            if ($attempt->isExpired()) {
                $this->gradeAttempt($attempt, true);
            }
        }

        // Ambil riwayat pengerjaan user login
        $attempts = QuestionAttempt::where('user_id', Auth::id())
            ->with('questionPackage')
            ->latest()
            ->paginate(5, ['*'], 'history_page');

        if ($request->ajax()) {
            return view('exams._history_table', compact('attempts'))->render();
        }

        return view('exams.index', compact('packages', 'attempts'));
    }

    /**
    * Tampilkan riwayat pengerjaan ujian (halaman terpisah).
    */
    public function history()
    {
        $userId = Auth::id();
        $attempts = QuestionAttempt::where('user_id', $userId)
            ->with('questionPackage')
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        return view('exams.history', compact('attempts'));
    }


    /**
     * Mulai pengerjaan paket soal (Ujian Baru).
     */
    public function start(QuestionPackage $questionPackage) {
        // Validasi: Apakah paket sudah dipublikasikan & punya soal?
        if (!$questionPackage->is_published || !$questionPackage->hasMinimumQuestions()) {
            return redirect()->route('exams.index')
                ->with('error', 'Paket soal tidak tersedia atau belum siap.');
        }

        $userId = Auth::id();

        // Validasi: Batas Attempt
        if (!is_null($questionPackage->attempt_limit)) {
            $existingAttemptsCount = QuestionAttempt::where('user_id', $userId)
                ->where('question_package_id', $questionPackage->id)
                ->where('is_completed', true)
                ->count();

            if ($existingAttemptsCount >= $questionPackage->attempt_limit) {
                return redirect()->route('exams.index')
                    ->with('error', "Anda telah mencapai batas maksimal pengerjaan ({$questionPackage->attempt_limit} kali) untuk paket ini.");
            }
        }

        // Cek apakah ada attempt yang masih berjalan (InProgress)
        $activeAttempt = QuestionAttempt::where('user_id', $userId)
            ->inProgress()
            ->first();

        if ($activeAttempt) {
            // Jika sudah expired (waktu habis), auto-submit attempt tersebut
            if ($activeAttempt->isExpired()) {
                $this->gradeAttempt($activeAttempt, true);
            } else {
                // Jika paket yang dikerjakan sama, lanjutkan
                if ($activeAttempt->question_package_id === $questionPackage->id) {
                    return redirect()->route('exams.attempt', $activeAttempt->id);
                }
                
                // Blokir jika mencoba memulai paket lain
                return redirect()->route('exams.index')
                    ->with('error', 'Anda masih memiliki ujian pada paket lain yang sedang berjalan. Selesaikan ujian tersebut terlebih dahulu.');
            }
        }

        // Buat attempt baru
        $attempt = QuestionAttempt::create([
            'user_id' => $userId,
            'question_package_id' => $questionPackage->id,
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Ambil semua soal aktif
        $questions = $questionPackage->activeQuestions()->get();
        $questionIds = $questions->pluck('id')->toArray();

        // 1. Logika Pengacakan Soal
        if ($questionPackage->shuffle_questions) {
            shuffle($questionIds);
        } else {
            // Urutkan berdasarkan kolom order
            $questionIds = $questionPackage->activeQuestions()
                ->ordered()
                ->pluck('id')
                ->toArray();
        }

        // 2. Logika Pengacakan Jawaban per Soal
        $optionsOrder = [];
        foreach ($questions as $question) {
            // Dapatkan label opsi yang tersedia (misal: A, B, C, D)
            $labels = $question->options->pluck('option_label')->toArray();
            
            if ($questionPackage->shuffle_answers) {
                shuffle($labels);
            }
            $optionsOrder[$question->id] = $labels;
        }

        // Simpan urutan acak ini di session agar stateless & konsisten selama attempt
        Session::put("attempt_{$attempt->id}_questions", $questionIds);
        Session::put("attempt_{$attempt->id}_options", $optionsOrder);

        // Inisialisasi draft response kosong untuk semua soal agar mempermudah navigasi
        foreach ($questionIds as $qId) {
            QuestionResponse::create([
                'question_attempt_id' => $attempt->id,
                'question_id' => $qId,
                'selected_answer' => null,
                'is_correct' => null,
            ]);
        }

        return redirect()->route('exams.attempt', $attempt->id);
    }

    /**
     * Lembar Ujian (Tampilan pengerjaan soal).
     */
    public function attempt(QuestionAttempt $questionAttempt, Request $request) {
        // Proteksi akses
        Gate::authorize('view', $questionAttempt);

        // Cek jika sudah selesai
        if ($questionAttempt->is_completed || $questionAttempt->isFinished()) {
            return redirect()->route('exams.results', $questionAttempt->id);
        }

        // Cek jika durasi sudah habis
        if ($questionAttempt->isExpired()) {
            $this->gradeAttempt($questionAttempt, true);
            return redirect()->route('exams.results', $questionAttempt->id)
                ->with('warning', 'Waktu Anda telah habis! Jawaban telah otomatis disimpan.');
        }

        // Ambil urutan soal dari session
        $questionIds = Session::get("attempt_{$questionAttempt->id}_questions");
        $optionsOrder = Session::get("attempt_{$questionAttempt->id}_options");

        // Fallback jika session hilang (misal session server expired di tengah jalan)
        if (!$questionIds || !$optionsOrder) {
            // Re-generate urutan default (tanpa acak untuk penyelamatan data)
            $questionIds = $questionAttempt->responses()->pluck('question_id')->toArray();
            $optionsOrder = [];
            
            // Ambil semua soal untuk mendapatkan label opsinya
            $questionsInAttempt = Question::with('options')->whereIn('id', $questionIds)->get()->keyBy('id');
            
            foreach ($questionIds as $qId) {
                $q = $questionsInAttempt[$qId] ?? null;
                $optionsOrder[$qId] = $q ? $q->options->pluck('option_label')->toArray() : ['A', 'B', 'C', 'D', 'E'];
            }
            Session::put("attempt_{$questionAttempt->id}_questions", $questionIds);
            Session::put("attempt_{$questionAttempt->id}_options", $optionsOrder);
        }

        // Tentukan nomor soal aktif (default 1 / soal pertama)
        $currentNumber = $request->query('page', 1);
        if ($currentNumber < 1 || $currentNumber > count($questionIds)) {
            $currentNumber = 1;
        }

        $activeQuestionId = $questionIds[$currentNumber - 1];

        // Ambil detail soal aktif
        $question = Question::with('options')->find($activeQuestionId);

        // Susun opsi jawaban sesuai urutan acak yang disimpan di session
        $shuffledLabels = $optionsOrder[$question->id] ?? $question->options->pluck('option_label')->toArray();
        $options = [];
        foreach ($shuffledLabels as $label) {
            $opt = $question->options->where('option_label', $label)->first();
            if ($opt) {
                $options[] = $opt;
            }
        }

        // Dapatkan jawaban draft saat ini
        $currentResponse = QuestionResponse::where('question_attempt_id', $questionAttempt->id)
            ->where('question_id', $question->id)
            ->first();

        // Dapatkan status pengerjaan seluruh soal untuk panel navigasi (Sidebar nomor soal)
        $responsesStatus = QuestionResponse::where('question_attempt_id', $questionAttempt->id)
            ->with('question')
            ->get()
            ->keyBy('question_id');

        $navigation = [];
        foreach ($questionIds as $index => $qId) {
            $resp = $responsesStatus[$qId] ?? null;
            $isAnswered = false;
            if ($resp) {
                $isAnswered = !$resp->isUnanswered();
            }
            $navigation[] = [
                'number' => $index + 1,
                'question_id' => $qId,
                'is_answered' => $isAnswered,
                'is_active' => ($index + 1) == $currentNumber,
            ];
        }

        $timeRemaining = $questionAttempt->getTimeRemaining();
        $endTime = $questionAttempt->started_at->copy()->addMinutes($questionAttempt->questionPackage->duration_minutes)->getPreciseTimestamp(3);

        return response()->view('exams.attempt', compact(
            'questionAttempt',
            'question',
            'options',
            'currentResponse',
            'navigation',
            'currentNumber',
            'timeRemaining',
            'endTime',
            'questionIds'
        ))
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Simpan jawaban draft secara dinamis (via form submit / AJAX).
     */
    public function saveResponse(Request $request, QuestionAttempt $questionAttempt) {
        // Proteksi: Cek otorisasi, status selesai, dan durasi waktu
        if (Auth::user()->cannot('saveResponse', $questionAttempt) || $questionAttempt->is_completed) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        if ($questionAttempt->isExpired()) {
            $this->gradeAttempt($questionAttempt, true);
            return response()->json(['error' => 'Waktu ujian telah berakhir.'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'nullable|in:A,B,C,D,E',
            'essay_answer' => 'nullable|string',
        ]);

        // Simpan jawaban draft
        QuestionResponse::updateOrCreate(
            [
                'question_attempt_id' => $questionAttempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_answer' => $request->selected_answer,
                'essay_answer' => $request->essay_answer,
            ]
        );

        // Jika request dikirim dari form biasa (non-AJAX / fallback)
        if (!$request->ajax()) {
            $nextPage = $request->input('next_page');
            if ($nextPage) {
                return redirect()->route('exams.attempt', [$questionAttempt->id, 'page' => $nextPage]);
            }
            return redirect()->back();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Selesai & Submit Ujian.
     */
    public function submit(QuestionAttempt $questionAttempt, Request $request) {
        Gate::authorize('submit', $questionAttempt);

        if ($questionAttempt->is_completed) {
            return redirect()->route('exams.results', $questionAttempt->id);
        }

        // Cek apakah ada auto-submit flag dari timer JS
        $isAutoSubmitted = $request->has('auto_submitted') && $request->auto_submitted == '1';

        // Lakukan perhitungan skor (grading)
        $this->gradeAttempt($questionAttempt, $isAutoSubmitted);

        // Hapus session pengacakan agar bersih
        Session::forget("attempt_{$questionAttempt->id}_questions");
        Session::forget("attempt_{$questionAttempt->id}_options");

        return redirect()->route('exams.results', $questionAttempt->id)
            ->with('success', 'Ujian Anda berhasil diserahkan dan dinilai!');
    }

    /**
     * Halaman Hasil Ujian & Review Evaluasi.
     */
    public function results(QuestionAttempt $questionAttempt) {
        Gate::authorize('view', $questionAttempt);

        if (!$questionAttempt->is_completed) {
            return redirect()->route('exams.attempt', $questionAttempt->id);
        }

        $stats = $questionAttempt->getAnswerStatistics();
        $package = $questionAttempt->questionPackage;

        // Ambil semua respon jawaban beserta soal & opsinya untuk di-review
        $responses = QuestionResponse::where('question_attempt_id', $questionAttempt->id)
            ->with(['question.options'])
            ->get();

        if (!$package) {
            return redirect()->route('exams.index')
                ->with('error', 'Paket soal telah dihapus atau tidak tersedia.');
        }

        return view('exams.results', compact('questionAttempt', 'stats', 'package', 'responses'));
    }

    /**
     * Hitung Skor Akhir (Grading Logics).
     */
    private function gradeAttempt(QuestionAttempt $attempt, bool $isAutoSubmitted = false) {
        DB::transaction(function () use ($attempt, $isAutoSubmitted) {
            $responses = $attempt->responses()->with('question')->get();
            $correctCount = 0;
            $totalQuestions = $attempt->questionPackage->activeQuestions()->count();

            foreach ($responses as $response) {
                $question = $response->question;
                
                // Jika dijawab dan jawabannya benar
                $isCorrect = false;

                if ($question->isMultipleChoice()) {
                    if (!is_null($response->selected_answer)) {
                        $isCorrect = $response->selected_answer === $question->correct_answer;
                    }
                } elseif ($question->isEssay()) {
                    if (!is_null($response->essay_answer)) {
                        // Mekanisme isian lebih fleksibel: normalize whitespace, trim, dan case-insensitive
                        $userAnswer = preg_replace('/\s+/', ' ', trim($response->essay_answer));
                        $correctAnswer = preg_replace('/\s+/', ' ', trim($question->correct_answer));
                        $isCorrect = mb_strtolower($userAnswer) === mb_strtolower($correctAnswer);
                    }
                }
                
                if ($isCorrect) {
                    $correctCount++;
                }

                $response->update([
                    'is_correct' => $isCorrect
                ]);
            }

            // Hitung Score Akhir (0-100)
            $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

            // Hitung Waktu spent
            $started = $attempt->started_at;
            $finished = now();
            $timeSpent = $finished->diffInSeconds($started);

            // Batasi time spent maksimal sesuai durasi paket
            $maxSeconds = $attempt->questionPackage->duration_minutes * 60;
            if ($timeSpent > $maxSeconds) {
                $timeSpent = $maxSeconds;
            }

            $attempt->update([
                'finished_at' => $finished,
                'time_spent_seconds' => $timeSpent,
                'total_score' => $score,
                'is_auto_submitted' => $isAutoSubmitted,
                'is_completed' => true,
            ]);
        });
    }
}
