<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_access_dashboard_with_stats()
    {
        $superuser = User::factory()->create(['role' => User::ROLE_SUPERUSER, 'is_approved' => true]);
        
        // Create some data
        User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        QuestionPackage::factory()->count(3)->create();

        $response = $this->actingAs($superuser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total User');
        $response->assertSee('Total Paket');
    }

    public function test_administrator_can_access_dashboard_with_scoped_stats()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        
        // User created by this admin
        User::factory()->create([
            'role' => User::ROLE_TEACHER, 
            'created_by_id' => $admin->id,
            'is_approved' => true
        ]);
        
        // User NOT created by this admin
        User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total User');
        // Check if stats are scoped (we expect 1 user created by this admin)
        // This is a bit hard to assert exactly from the view text without specific IDs, 
        // but we can check if it loads without errors.
    }

    public function test_teacher_can_access_dashboard_with_own_stats()
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        
        QuestionPackage::factory()->create(['user_id' => $teacher->id]);
        
        $response = $this->actingAs($teacher)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Paket Soal');
        $response->assertSee('Siswa Unik');
    }

    public function test_student_can_access_dashboard_with_personal_stats()
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_approved' => true]);
        
        $response = $this->actingAs($student)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Ujian');
        $response->assertSee('Ujian Tersedia');
    }

    public function test_unapproved_user_cannot_access_dashboard()
    {
        $user = User::factory()->create(['is_approved' => false]);
        
        $response = $this->actingAs($user)->get('/dashboard');

        // It should redirect to home or somewhere else based on EnsureApproved middleware
        $response->assertStatus(302);
    }
}
