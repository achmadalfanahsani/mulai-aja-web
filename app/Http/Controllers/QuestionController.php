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
            $rules['correct_answer'] = 'required|in:A,B,C,D,E';
            $rules['options'] = 'required|array|size:5';
            $rules['options.A'] = 'required|string';
            $rules['options.B'] = 'required|string';
            $rules['options.C'] = 'required|string';
            $rules['options.D'] = 'required|string';
            $rules['options.E'] = 'required|string';
        } else {
            $rules['correct_answer_essay'] = 'required|string';
        }

        $request->validate($rules);

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
            $rules['correct_answer'] = 'required|in:A,B,C,D,E';
            $rules['options'] = 'required|array|size:5';
            $rules['options.A'] = 'required|string';
            $rules['options.B'] = 'required|string';
            $rules['options.C'] = 'required|string';
            $rules['options.D'] = 'required|string';
            $rules['options.E'] = 'required|string';
        } else {
            $rules['correct_answer_essay'] = 'required|string';
        }

        $request->validate($rules);

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

            // Update 5 opsi jika pilihan ganda
            if ($request->question_type === 'multiple_choice') {
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
