<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grades = ['X', 'XI', 'XII'];
        $majors = ['IPA', 'IPS', 'Bahasa', 'RPL', 'TKJ', 'Multimedia'];
        $nums = ['1', '2', '3', '4'];

        $name = fake()->randomElement($grades) . ' ' . fake()->randomElement($majors) . ' ' . fake()->randomElement($nums);

        return [
            'name' => $name,
            'teacher_id' => User::factory()->teacher(),
            'description' => fake()->sentence(),
        ];
    }
}
