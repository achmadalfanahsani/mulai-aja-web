<?php

namespace Tests\Feature;

use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_exams_index_shows_package_type_labels()
    {
        $user = User::factory()->administrator()->create();
        $this->actingAs($user);

        // Create packages of different types
        $packageMc = QuestionPackage::factory()->create([
            'name' => 'Paket Pilihan Ganda',
            'package_type' => 'multiple_choice',
            'user_id' => $user->id,
            'is_published' => true
        ]);
        
        $packageEssay = QuestionPackage::factory()->create([
            'name' => 'Paket Isian',
            'package_type' => 'essay',
            'user_id' => $user->id,
            'is_published' => true
        ]);

        $response = $this->get(route('exams.index'));

        $response->assertStatus(200);
        $response->assertSee('Paket Pilihan Ganda');
        $response->assertSee('Pilihan Ganda'); // Label
        $response->assertSee('bg-success'); // Badge class

        $response->assertSee('Paket Isian');
        $response->assertSee('Isian Singkat'); // Label
        $response->assertSee('bg-info'); // Badge class
    }

    public function test_exams_index_shows_classroom_info()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $classroom = \App\Models\Classroom::factory()->create([
            'name' => 'Kelas X RPL 1'
        ]);
        $classroom->students()->attach($user->id);

        $package = QuestionPackage::factory()->create([
            'name' => 'Ujian Akhir Semester',
            'is_published' => true,
            'user_id' => User::factory()->teacher()->create()->id
        ]);
        $package->classrooms()->attach($classroom->id);

        $response = $this->get(route('exams.index'));

        $response->assertStatus(200);
        $response->assertSee('Ujian Akhir Semester');
        $response->assertSee('Kelas X RPL 1');
        $response->assertSee('data-package-classrooms="Kelas X RPL 1"', false);
    }
}
