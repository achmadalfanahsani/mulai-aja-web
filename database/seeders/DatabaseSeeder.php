<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Run Role and Permission Seeder
        $this->call(RoleAndPermissionSeeder::class);

        // Create additional student users for testing
        User::factory()
            ->count(5)
            ->state(fn () => ['role' => 'student', 'is_approved' => true])
            ->create();

        echo "✅ Database seeded successfully!\n";

        // Run question package seeder
        $this->call(QuestionPackageSeeder::class);
    }
}
