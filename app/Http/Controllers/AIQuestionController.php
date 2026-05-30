<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionPackage;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AIQuestionController extends Controller {
    protected $aiService;

    public function __construct(AIService $aiService) {
        $this->aiService = $aiService;
    }

    /**
     * Generate questions using AI and save to database.
     */
    public function generate(Request $request, QuestionPackage $questionPackage) {
        Gate::authorize('create', [Question::class, $questionPackage]);

        $request->validate([
            'raw_questions' => 'required|string|min:10',
        ]);

        try {
            $generatedData = $this->aiService->generateMultipleChoice($request->raw_questions);

            if (empty($generatedData)) {
                return redirect()->back()->with('error', 'AI gagal menghasilkan soal. Pastikan format input benar.');
            }

            DB::transaction(function () use ($generatedData, $questionPackage) {
                $lastOrder = $questionPackage->questions()->max('order') ?? 0;

                foreach ($generatedData as $data) {
                    $lastOrder++;

                    $question = Question::create([
                        'question_package_id' => $questionPackage->id,
                        'question_type' => Question::TYPE_MULTIPLE_CHOICE,
                        'question_text' => $data['question_text'],
                        'explanation' => $data['explanation'] ?? null,
                        'correct_answer' => $data['correct_answer'],
                        'difficulty_level' => 'medium',
                        'order' => $lastOrder,
                        'is_active' => true,
                    ]);

                    foreach ($data['options'] as $label => $text) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_label' => $label,
                            'option_text' => $text,
                        ]);
                    }

                    $questionPackage->increment('total_questions_count');
                }
            });

            return redirect()->route('question-packages.questions.index', [$questionPackage->id, 'type' => $questionPackage->package_type])
                ->with('success', count($generatedData) . ' soal berhasil digenerate dan ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
