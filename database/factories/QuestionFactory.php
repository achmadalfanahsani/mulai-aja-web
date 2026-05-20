<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory {
    protected $model = Question::class;

    public function definition(): array {
        return [
            'question_package_id' => QuestionPackage::factory(),
            'question_text' => $this->faker->sentence(20) . '?',
            'explanation' => $this->faker->paragraph(),
            'question_image_path' => null, // Default no image
            'correct_answer' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'difficulty_level' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'order' => 0, // Will be updated
            'is_active' => true,
        ];
    }

    /**
     * State: Dengan image
     */
    public function withImage(): self {
        return $this->state(fn (array $attributes) => [
            'question_image_path' => 'questions/sample-' . uniqid() . '.jpg',
        ]);
    }

    /**
     * State: Easy difficulty
     */
    public function easy(): self {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => 'easy',
        ]);
    }

    /**
     * State: Medium difficulty
     */
    public function medium(): self {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => 'medium',
        ]);
    }

    /**
     * State: Hard difficulty
     */
    public function hard(): self {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => 'hard',
        ]);
    }
}
