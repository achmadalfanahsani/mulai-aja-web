<?php

namespace Tests\Feature;

use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPackageTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_mixed_package_type()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
        $this->actingAs($user);

        $response = $this->post(route('question-packages.store'), [
            'name' => 'Paket Campuran',
            'package_type' => 'mixed',
            'duration_minutes' => 60,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['package_type']);
    }

    public function test_can_create_multiple_choice_package()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
        $this->actingAs($user);

        $response = $this->post(route('question-packages.store'), [
            'name' => 'Paket Pilihan Ganda',
            'package_type' => 'multiple_choice',
            'duration_minutes' => 60,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('question_packages', [
            'name' => 'Paket Pilihan Ganda',
            'package_type' => 'multiple_choice',
        ]);
    }

    public function test_can_create_essay_package()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create([
            'role' => 'administrator',
            'is_approved' => true
        ]);
        $this->actingAs($user);

        $response = $this->post(route('question-packages.store'), [
            'name' => 'Paket Essay',
            'package_type' => 'essay',
            'duration_minutes' => 60,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('question_packages', [
            'name' => 'Paket Essay',
            'package_type' => 'essay',
        ]);
    }
}
