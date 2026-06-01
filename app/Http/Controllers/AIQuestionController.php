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

        $lockKey = 'generating_soal_' . $questionPackage->id;
        $lock = \Illuminate\Support\Facades\Cache::lock($lockKey, 120); // Kunci selama 120 detik

        if (!$lock->get()) {
            return redirect()->back()->with('error', 'Proses pembuatan soal sedang berjalan, mohon tunggu beberapa saat...');
        }

        try {
            \Illuminate\Support\Facades\Log::info('=== AI GENERATE v3 === Package: ' . $questionPackage->id);

            if ($questionPackage->package_type === 'essay') {
                $generatedData = $this->aiService->generateEssay($request->raw_questions);
            } else {
                $generatedData = $this->aiService->generateMultipleChoice($request->raw_questions);
            }

            \Illuminate\Support\Facades\Log::info('AI returned ' . count($generatedData) . ' soal (setelah deduplikasi). Data: ' . json_encode(array_column($generatedData, 'question_text')));

            if (empty($generatedData)) {
                return redirect()->back()->with('error', 'AI gagal menghasilkan soal. Pastikan format input benar.');
            }

            DB::transaction(function () use ($generatedData, $questionPackage) {
                $lastOrder = $questionPackage->questions()->max('order') ?? 0;

                foreach ($generatedData as $data) {
                    $lastOrder++;

                    $question = Question::create([
                        'question_package_id' => $questionPackage->id,
                        'question_type' => $questionPackage->package_type === 'essay' ? Question::TYPE_ESSAY : Question::TYPE_MULTIPLE_CHOICE,
                        'question_text' => $data['question_text'],
                        'explanation' => $data['explanation'] ?? null,
                        'correct_answer' => $data['correct_answer'],
                        'difficulty_level' => 'medium',
                        'order' => $lastOrder,
                        'is_active' => true,
                    ]);

                    if ($questionPackage->package_type !== 'essay' && isset($data['options'])) {
                        foreach ($data['options'] as $label => $text) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_label' => $label,
                                'option_text' => $text,
                            ]);
                        }
                    }

                    $questionPackage->increment('total_questions_count');
                }
            });

            return redirect()->route('question-packages.questions.index', [$questionPackage->id, 'type' => $questionPackage->package_type])
                ->with('success', count($generatedData) . ' soal berhasil digenerate dan ditambahkan! (v3)');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }
}
