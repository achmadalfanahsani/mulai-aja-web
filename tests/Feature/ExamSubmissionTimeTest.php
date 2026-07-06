<?php

namespace Tests\Feature;

use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ExamSubmissionTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_submission_calculates_non_negative_integer_time_spent()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'duration_minutes' => 60,
            'is_published' => true,
        ]);

        // Start exam attempt at 12:00:00
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:00:00'));
        $attempt = QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Move clock forward to 12:35:30 (35.5 minutes later)
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:35:30'));

        $response = $this->post(route('exams.submit', $attempt->id), [
            'auto_submitted' => '0',
        ]);

        $response->assertStatus(302);
        
        $attempt->refresh();
        $this->assertTrue($attempt->is_completed);
        
        // Time spent should be an integer, absolute difference: 35 minutes * 60 + 30 seconds = 2130 seconds
        $this->assertEquals(2130, $attempt->time_spent_seconds);
        $this->assertIsInt($attempt->time_spent_seconds);

        Carbon::setTestNow();
    }

    public function test_exam_submission_caps_time_spent_at_max_duration()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'duration_minutes' => 15,
            'is_published' => true,
        ]);

        // Start exam attempt at 12:00:00
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:00:00'));
        $attempt = QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Move clock forward past duration (e.g. 20 minutes)
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:20:00'));

        $response = $this->post(route('exams.submit', $attempt->id), [
            'auto_submitted' => '0',
        ]);

        $response->assertStatus(302);
        
        $attempt->refresh();
        $this->assertTrue($attempt->is_completed);
        
        // Time spent should be capped at duration_minutes * 60 = 900 seconds
        $this->assertEquals(900, $attempt->time_spent_seconds);

        Carbon::setTestNow();
    }
}
