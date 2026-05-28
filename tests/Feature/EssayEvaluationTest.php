<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Models\QuestionPackage;
use App\Models\QuestionResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EssayEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_essay_statistics_and_evaluation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'package_type' => 'essay',
            'duration_minutes' => 60
        ]);

        $q1 = Question::create([
            'question_package_id' => $package->id,
            'question_text' => 'What is 1+1?',
            'question_type' => 'essay',
            'correct_answer' => '2',
            'order' => 1
        ]);

        $q2 = Question::create([
            'question_package_id' => $package->id,
            'question_text' => 'What is 2+2?',
            'question_type' => 'essay',
            'correct_answer' => '4',
            'order' => 2
        ]);

        $attempt = QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
        ]);

        // Answer q1 correctly
        QuestionResponse::create([
            'question_attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'essay_answer' => '2',
        ]);

        // Answer q2 incorrectly
        QuestionResponse::create([
            'question_attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'essay_answer' => '5',
        ]);

        // Verify statistics before grading
        $stats = $attempt->getAnswerStatistics();
        $this->assertEquals(2, $stats['total_questions']);
        $this->assertEquals(2, $stats['answered_count']);
        $this->assertEquals(0, $stats['unanswered_count']);

        // Grade attempt
        // We need to call the private method or simulate it
        // The gradeAttempt logic is in ExamController, but it's private.
        // We can simulate it here or use a helper.
        
        foreach ($attempt->responses as $response) {
            $isCorrect = strtolower(trim($response->essay_answer)) === strtolower(trim($response->question->correct_answer));
            $response->update(['is_correct' => $isCorrect]);
        }
        $attempt->update(['is_completed' => true, 'total_score' => 50]);

        // Verify statistics after grading
        $stats = $attempt->getAnswerStatistics();
        $this->assertEquals(1, $stats['correct_count']);
        $this->assertEquals(1, $stats['wrong_count']);
    }

    public function test_unanswered_essay_statistics()
    {
        $user = User::factory()->create(['role' => 'student']);
        
        $package = QuestionPackage::factory()->create(['package_type' => 'essay']);
        $q1 = Question::create([
            'question_package_id' => $package->id,
            'question_text' => 'Q1',
            'question_type' => 'essay',
            'correct_answer' => 'A',
            'order' => 1
        ]);

        $attempt = QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
        ]);

        // No response created yet
        $stats = $attempt->getAnswerStatistics();
        $this->assertEquals(1, $stats['total_questions']); // Counts active questions in package

        // Create empty response
        QuestionResponse::create([
            'question_attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'essay_answer' => '',
        ]);

        $stats = $attempt->getAnswerStatistics();
        $this->assertEquals(1, $stats['total_questions']);
        $this->assertEquals(0, $stats['answered_count']);
        $this->assertEquals(1, $stats['unanswered_count']);
        
        // Update to non-empty
        $attempt->responses()->first()->update(['essay_answer' => 'some answer']);
        $stats = $attempt->getAnswerStatistics();
        $this->assertEquals(1, $stats['answered_count']);
        $this->assertEquals(0, $stats['unanswered_count']);
    }
}
