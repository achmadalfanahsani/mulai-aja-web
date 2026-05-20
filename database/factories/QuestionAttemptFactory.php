<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionAttemptFactory extends Factory {
    protected $model = QuestionAttempt::class;

    public function definition(): array {
        $startedAt = $this->faker->dateTimeThisMonth();
        $duration = $this->faker->numberBetween(300, 3600); // 5 min - 60 min
        
        return [
            'user_id' => User::factory(),
            'question_package_id' => QuestionPackage::factory(),
            'started_at' => $startedAt,
            'finished_at' => now($startedAt)->addSeconds($duration),
            'time_spent_seconds' => $duration,
            'total_score' => $this->faker->numberBetween(0, 100),
            'is_auto_submitted' => $this->faker->boolean(20), // 20% auto-submitted
            'is_completed' => true,
        ];
    }

    /**
     * State: Sedang dalam progress (belum selesai)
     */
    public function inProgress(): self {
        return $this->state(fn (array $attributes) => [
            'finished_at' => null,
            'time_spent_seconds' => null,
            'total_score' => null,
            'is_completed' => false,
        ]);
    }

    /**
     * State: Auto-submitted
     */
    public function autoSubmitted(): self {
        return $this->state(fn (array $attributes) => [
            'is_auto_submitted' => true,
        ]);
    }

    /**
     * State: Dengan score tertentu
     */
    public function withScore($score): self {
        return $this->state(fn (array $attributes) => [
            'total_score' => $score,
        ]);
    }
}
