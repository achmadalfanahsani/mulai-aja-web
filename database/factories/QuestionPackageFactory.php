<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\QuestionPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionPackageFactory extends Factory {
    protected $model = QuestionPackage::class;

    public function definition(): array {
        return [
            'user_id' => User::factory(), // Create teacher user
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'is_published' => $this->faker->boolean(70), // 70% published
            'shuffle_questions' => true,
            'shuffle_answers' => true,
            'passing_score' => $this->faker->randomElement([60, 70, 80, null]),
            'total_questions_count' => 0, // Will be updated
        ];
    }

    /**
     * State: Draft paket (belum dipublikasi)
     */
    public function draft(): self {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * State: Published paket
     */
    public function published(): self {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * State: Dengan passing score
     */
    public function withPassingScore($score = 75): self {
        return $this->state(fn (array $attributes) => [
            'passing_score' => $score,
        ]);
    }
}
