<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Superuser
        User::updateOrCreate(
            ['email' => 'superuser@example.com'],
            [
                'name' => 'Super User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPERUSER,
                'is_approved' => true,
            ]
        );

        // Create a default Administrator (Approved)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMINISTRATOR,
                'is_approved' => true,
            ]
        );

        // Create a default Teacher
        User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Guru Teladan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TEACHER,
                'is_approved' => true,
            ]
        );

        // Create a default Student
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Siswa Pintar',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
                'is_approved' => true,
            ]
        );
    }
}
