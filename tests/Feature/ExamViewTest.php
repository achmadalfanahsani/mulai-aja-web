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
        $this->withoutMiddleware();
        
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
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
}
