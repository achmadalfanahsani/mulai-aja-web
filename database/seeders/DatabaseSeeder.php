<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Create admin user (untuk testing)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Create teacher user
        User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
        ]);

        // Create student users
        User::factory()
            ->count(10)
            ->state(fn () => ['role' => 'student'])
            ->create();

        echo "✅ DatabaseSeeder: " . (1 + 1 + 10) . " users dibuat\n";

        // Run question package seeder
        $this->call(QuestionPackageSeeder::class);
    }
}
