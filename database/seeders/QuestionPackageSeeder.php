<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionPackage;
use Illuminate\Database\Seeder;

class QuestionPackageSeeder extends Seeder {
    public function run(): void {
        // Create teachers
        $teachers = User::factory()
            ->count(2)
            ->state(fn () => ['role' => 'teacher'])
            ->create();

        // Create packages for each teacher
        foreach ($teachers as $teacher) {
            // Create 3 published packages per teacher
            for ($p = 0; $p < 3; $p++) {
                $package = QuestionPackage::factory()
                    ->published()
                    ->for($teacher)
                    ->create([
                        'name' => "Paket Soal " . ($p + 1) . " - " . $teacher->name,
                    ]);

                // Create questions for this package
                $questionsCount = rand(5, 10);
                for ($q = 0; $q < $questionsCount; $q++) {
                    $question = Question::factory()
                        ->for($package)
                        ->create([
                            'order' => $q + 1,
                        ]);

                    // Create 5 options (A-E) for this question
                    $labels = ['A', 'B', 'C', 'D', 'E'];
                    foreach ($labels as $idx => $label) {
                        QuestionOption::factory()
                            ->for($question)
                            ->create([
                                'option_label' => $label,
                                'option_text' => "Opsi " . $label . " - " . 
                                    ($label === $question->correct_answer ? '[BENAR]' : '[salah]'),
                            ]);
                    }
                }

                // Update cache column
                $package->update([
                    'total_questions_count' => $questionsCount,
                ]);
            }

            // Create 2 draft packages per teacher
            for ($p = 0; $p < 2; $p++) {
                $package = QuestionPackage::factory()
                    ->draft()
                    ->for($teacher)
                    ->create([
                        'name' => "Draft Paket - " . $teacher->name . " (" . ($p + 1) . ")",
                    ]);

                $questionsCount = rand(3, 5);
                for ($q = 0; $q < $questionsCount; $q++) {
                    $question = Question::factory()
                        ->for($package)
                        ->create(['order' => $q + 1]);

                    $labels = ['A', 'B', 'C', 'D', 'E'];
                    foreach ($labels as $label) {
                        QuestionOption::factory()
                            ->for($question)
                            ->create(['option_label' => $label]);
                    }
                }

                $package->update([
                    'total_questions_count' => $questionsCount,
                ]);
            }
        }

        echo "✅ QuestionPackageSeeder berhasil dijalankan!\n";
        echo "   - " . $teachers->count() . " teachers dibuat\n";
        echo "   - Masing-masing teacher memiliki 3 published + 2 draft packages\n";
    }
}
