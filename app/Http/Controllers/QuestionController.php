<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller {
    /**
     * Tampilkan semua soal di dalam suatu paket soal.
     */
    public function index(QuestionPackage $questionPackage) {
        Gate::authorize('viewAny', [Question::class, $questionPackage]);
        $questions = $questionPackage->questions()->ordered()->get();
        return view('questions.index', compact('questionPackage', 'questions'));
    }

    /**
     * Tampilkan form pembuatan soal baru.
     */
    public function create(QuestionPackage $questionPackage) {
        Gate::authorize('create', [Question::class, $questionPackage]);
        return view('questions.create', compact('questionPackage'));
    }

    /**
     * Simpan soal baru beserta 5 opsi jawabannya.
     */
    public function store(Request $request, QuestionPackage $questionPackage) {
        Gate::authorize('create', [Question::class, $questionPackage]);
        $rules = [
            'question_type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'difficulty_level' => 'nullable|in:easy,medium,hard',
        ];

        if ($request->question_type === 'multiple_choice') {
            $rules['correct_answer'] = 'required|string';
            $rules['options'] = 'required|array|min:2|max:5';
            $rules['options.*'] = 'required|string';
        } else {
            $rules['correct_answer_essay'] = 'required|string';
        }

        $request->validate($rules);

        // Validasi tambahan: question_type harus sesuai dengan package_type (kecuali mixed)
        if ($questionPackage->package_type !== 'mixed' && $request->question_type !== $questionPackage->package_type) {
            return redirect()->back()->withErrors(['question_type' => 'Tipe soal harus sesuai dengan tipe paket soal ('. $questionPackage->type_label .').'])->withInput();
        }

        // Validasi tambahan: correct_answer harus ada di list label options
        if ($request->question_type === 'multiple_choice') {
            if (!array_key_exists($request->correct_answer, $request->options)) {
                return redirect()->back()->withErrors(['correct_answer' => 'Kunci jawaban harus merupakan salah satu dari opsi yang diisi.'])->withInput();
            }
        }

        DB::transaction(function () use ($request, $questionPackage) {
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store(
                    'questions/' . $questionPackage->id,
                    'public'
                );
            }

            // Dapatkan order terakhir
            $nextOrder = $questionPackage->questions()->max('order') + 1;

            $correctAnswer = $request->question_type === 'multiple_choice' 
                ? $request->correct_answer 
                : $request->correct_answer_essay;

            // Simpan Soal
            $question = Question::create([
                'question_package_id' => $questionPackage->id,
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'question_image_path' => $imagePath,
                'correct_answer' => $correctAnswer,
                'difficulty_level' => $request->difficulty_level ?? 'medium',
                'order' => $nextOrder,
                'is_active' => true,
            ]);

            // Simpan 5 opsi jika pilihan ganda
            if ($request->question_type === 'multiple_choice') {
                foreach ($request->options as $label => $text) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_label' => $label,
                        'option_text' => $text,
                    ]);
                }
            }

            // Update cache total questions di package
            $questionPackage->increment('total_questions_count');
        });

        return redirect()->route('question-packages.questions.index', [$questionPackage->id, 'type' => $questionPackage->package_type])
            ->with('success', 'Soal berhasil ditambahkan ke dalam paket!');
    }

    /**
     * Tampilkan form edit soal.
     */
    public function edit(Question $question) {
        Gate::authorize('update', $question);
        $questionPackage = $question->questionPackage;
        // Ambil opsi yang ada dipetakan ke key A-E
        $options = $question->options->pluck('option_text', 'option_label')->toArray();
        return view('questions.edit', compact('questionPackage', 'question', 'options'));
    }

    /**
     * Perbarui soal beserta opsinya.
     */
    public function update(Request $request, Question $question) {
        Gate::authorize('update', $question);
        $questionPackage = $question->questionPackage;
        $rules = [
            'question_type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'difficulty_level' => 'nullable|in:easy,medium,hard',
        ];

        if ($request->question_type === 'multiple_choice') {
            $rules['correct_answer'] = 'required|string';
            $rules['options'] = 'required|array|min:2|max:5';
            $rules['options.*'] = 'required|string';
        } else {
            $rules['correct_answer_essay'] = 'required|string';
        }

        $request->validate($rules);

        // Validasi tambahan: question_type harus sesuai dengan package_type (kecuali mixed)
        if ($questionPackage->package_type !== 'mixed' && $request->question_type !== $questionPackage->package_type) {
            return redirect()->back()->withErrors(['question_type' => 'Tipe soal harus sesuai dengan tipe paket soal ('. $questionPackage->type_label .').'])->withInput();
        }

        // Validasi tambahan: correct_answer harus ada di list label options
        if ($request->question_type === 'multiple_choice') {
            if (!array_key_exists($request->correct_answer, $request->options)) {
                return redirect()->back()->withErrors(['correct_answer' => 'Kunci jawaban harus merupakan salah satu dari opsi yang diisi.'])->withInput();
            }
        }

        DB::transaction(function () use ($request, $questionPackage, $question) {
            $imagePath = $question->question_image_path;

            // Jika ada upload gambar baru
            if ($request->hasFile('question_image')) {
                // Hapus gambar lama jika ada
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $request->file('question_image')->store(
                    'questions/' . $questionPackage->id,
                    'public'
                );
            }

            $correctAnswer = $request->question_type === 'multiple_choice' 
                ? $request->correct_answer 
                : $request->correct_answer_essay;

            // Update Soal
            $question->update([
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'question_image_path' => $imagePath,
                'correct_answer' => $correctAnswer,
                'difficulty_level' => $request->difficulty_level ?? 'medium',
            ]);

            // Update opsi jika pilihan ganda
            if ($request->question_type === 'multiple_choice') {
                // Hapus opsi yang tidak ada di request (jika jumlah opsi berkurang)
                $question->options()->whereNotIn('option_label', array_keys($request->options))->delete();

                foreach ($request->options as $label => $text) {
                    QuestionOption::updateOrCreate(
                        [
                            'question_id' => $question->id,
                            'option_label' => $label
                        ],
                        [
                            'option_text' => $text
                        ]
                    );
                }
            } else {
                // Jika berubah dari pilihan ganda ke essay, hapus opsinya
                $question->options()->delete();
            }
        });

        return redirect()->route('question-packages.questions.index', [$questionPackage->id, 'type' => $questionPackage->package_type])
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Hapus soal dari database (soft delete).
     */
    public function destroy(Question $question) {
        Gate::authorize('delete', $question);
        $questionPackage = $question->questionPackage;
        DB::transaction(function () use ($questionPackage, $question) {
            // Soft delete question
            $question->delete();

            // Kurangi cache total questions di package
            if ($questionPackage->total_questions_count > 0) {
                $questionPackage->decrement('total_questions_count');
            }
        });

        return redirect()->route('question-packages.questions.index', [$questionPackage->id, 'type' => $questionPackage->package_type])
            ->with('success', 'Soal berhasil dihapus dari paket!');
    }
}
