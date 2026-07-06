<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class QuestionAttemptTimeSpentTest extends TestCase
{
    use RefreshDatabase;

    public function test_time_spent_and_expiry_calculations()
    {
        $user = User::factory()->create();
        $package = QuestionPackage::factory()->create([
            'duration_minutes' => 30,
        ]);

        // Mock started_at to be at 12:00:00
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:00:00'));
        
        $attempt = QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Move time forward by 10 minutes (600 seconds)
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:10:00'));

        $this->assertFalse($attempt->isExpired());
        $this->assertEquals(1200, $attempt->getTimeRemaining()); // 30 mins (1800s) - 10 mins (600s) = 20 mins (1200s)

        // Move time forward past duration (e.g. 35 minutes)
        Carbon::setTestNow(Carbon::parse('2026-07-05 12:35:00'));
        $this->assertTrue($attempt->isExpired());
        $this->assertEquals(0, $attempt->getTimeRemaining());

        // Reset Carbon
        Carbon::setTestNow();
    }
}
