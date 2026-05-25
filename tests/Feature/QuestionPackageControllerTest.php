<?php

namespace Tests\Feature;

use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPackageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_publish_status()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'user_id' => $user->id,
            'is_published' => false
        ]);

        // Tambahkan soal agar bisa dipublish
        $package->questions()->create([
            'question_text' => 'Contoh soal',
            'question_type' => 'multiple_choice',
            'is_active' => true,
            'correct_answer' => 'A',
            'order' => 1
        ]);

        // Publish
        $response = $this->post(route('question-packages.toggle-publish', $package->id));
        $response->assertStatus(302);
        
        $this->assertTrue($package->fresh()->is_published, 'Gagal mempublikasikan paket soal.');

        // Archive
        $response = $this->post(route('question-packages.toggle-publish', $package->id));
        $response->assertStatus(302);
        $this->assertFalse($package->fresh()->is_published, 'Gagal mengarsipkan paket soal.');
    }

    public function test_index_filter_preserves_type_parameter()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
        $this->actingAs($user);

        // Create packages of different types
        QuestionPackage::factory()->create(['name' => 'Paket Essay 1', 'package_type' => 'essay', 'user_id' => $user->id]);
        QuestionPackage::factory()->create(['name' => 'Paket Ganda 1', 'package_type' => 'multiple_choice', 'user_id' => $user->id]);

        // Filter by name while being in the 'essay' type context
        $response = $this->get(route('question-packages.index', [
            'type' => 'essay',
            'q' => 'Paket Essay'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Paket Essay 1');
        $response->assertDontSee('Paket Ganda 1');
        
        // Ensure the filter form in the response contains the hidden type input
        $response->assertSee('name="type" value="essay"', false);
    }
}
