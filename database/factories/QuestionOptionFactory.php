<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionOptionFactory extends Factory {
    protected $model = QuestionOption::class;

    private static $labels = ['A', 'B', 'C', 'D', 'E'];
    private static $labelIndex = 0;

    public function definition(): array {
        $label = self::$labels[self::$labelIndex % 5];
        self::$labelIndex++;

        return [
            'question_id' => Question::factory(),
            'option_label' => $label,
            'option_text' => $this->faker->sentence(10),
        ];
    }
}
