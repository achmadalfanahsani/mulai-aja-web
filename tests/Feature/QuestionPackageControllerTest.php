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
        $user = User::factory()->create(['role' => 'administrator']);
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'user_id' => $user->id,
            'is_published' => false
        ]);

        // Karena belum ada soal, harusnya gagal
        $response = $this->post(route('question-packages.toggle-publish', $package->id));
        $response->assertStatus(302); // Redirect back
        // Periksa apakah status tetap false
        $this->assertFalse($package->fresh()->is_published);
        // Kita tidak bisa assertion session error karena test ini redirect
        // tapi kita bisa pastikan status database tidak berubah

        // Tambahkan soal agar bisa dipublish
        $package->questions()->create([
            'question_text' => 'Contoh soal',
            'is_active' => true,
            'correct_answer' => 'A',
            'order' => 1
        ]);

        $response = $this->post(route('question-packages.toggle-publish', $package->id));
        $response->assertStatus(302);
        
        $response = $this->get($response->headers->get('Location'));
        $this->assertNotNull($response->getSession()->get('success'));
        $this->assertDatabaseHas('question_packages', ['id' => $package->id, 'is_published' => true]);

        // Toggle kembali ke draft
        $response = $this->post(route('question-packages.toggle-publish', $package->id));
        $this->assertDatabaseHas('question_packages', ['id' => $package->id, 'is_published' => false]);
    }
}
