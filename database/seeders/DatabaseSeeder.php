<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Run Role and Permission Seeder
        $this->call(RoleAndPermissionSeeder::class);

        // Run question package seeder (creates teachers and packages)
        $this->call(QuestionPackageSeeder::class);

        $teachers = \App\Models\User::where('role', \App\Models\User::ROLE_TEACHER)->get();
        $packages = \App\Models\QuestionPackage::all();

        // Create classrooms for each teacher
        foreach ($teachers as $teacher) {
            $classrooms = \App\Models\Classroom::factory()
                ->count(2)
                ->create();

            foreach ($classrooms as $classroom) {
                // Attach teacher to classroom
                $classroom->teachers()->attach($teacher->id);

                // Assign 3-5 random packages to each classroom
                $classroom->questionPackages()->attach(
                    $packages->random(rand(3, 5))->pluck('id')->toArray()
                );

                // Create 10-15 students for each classroom
                $students = \App\Models\User::factory()
                    ->student()
                    ->count(rand(10, 15))
                    ->create();

                $classroom->students()->attach($students->pluck('id')->toArray());

                // Generate some attempts for students in this classroom
                foreach ($students->take(5) as $student) {
                    $assignedPackages = $classroom->questionPackages;
                    if ($assignedPackages->isNotEmpty()) {
                        $packageToAttempt = $assignedPackages->random();
                        
                        \App\Models\QuestionAttempt::factory()
                            ->create([
                                'user_id' => $student->id,
                                'question_package_id' => $packageToAttempt->id,
                                'total_score' => rand(60, 100),
                                'is_completed' => true,
                            ]);
                    }
                }
            }
        }

        // Create some unapproved users for the dashboard
        \App\Models\User::factory()
            ->count(3)
            ->teacher()
            ->unapproved()
            ->create();

        \App\Models\User::factory()
            ->count(2)
            ->administrator()
            ->unapproved()
            ->create();

        echo "✅ Database seeded with a complete realistic ecosystem!\n";
    }
}
