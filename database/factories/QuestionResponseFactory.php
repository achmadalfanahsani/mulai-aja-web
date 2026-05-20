<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Models\QuestionResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionResponseFactory extends Factory {
    protected $model = QuestionResponse::class;

    public function definition(): array {
        $question = Question::factory()->create();
        
        return [
            'question_attempt_id' => QuestionAttempt::factory(),
            'question_id' => $question->id,
            'selected_answer' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'is_correct' => $this->faker->boolean(70), // 70% correct
            'time_spent_seconds' => $this->faker->numberBetween(10, 300),
        ];
    }

    /**
     * State: Correct answer
     */
    public function correct(): self {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }

    /**
     * State: Wrong answer
     */
    public function wrong(): self {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
        ]);
    }

    /**
     * State: Unanswered
     */
    public function unanswered(): self {
        return $this->state(fn (array $attributes) => [
            'selected_answer' => null,
            'is_correct' => false,
        ]);
    }
}
