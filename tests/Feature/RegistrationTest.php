<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * Test that a user can register as an administrator but it requires approval.
     */
    public function test_user_can_register_as_administrator()
    {
        $response = $this->postJson('/register', [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'administrator',
            'terms' => 'on',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals('Registrasi berhasil! Akun Anda sedang menunggu persetujuan dari Superuser.', session('success'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.com',
            'role' => 'administrator',
            'is_approved' => false,
        ]);
    }

    /**
     * Test that a user cannot register as a student.
     */
    public function test_user_cannot_register_as_student()
    {
        $response = $this->postJson('/register', [
            'name' => 'Student Test',
            'email' => 'student@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'terms' => 'on',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
        $this->assertDatabaseMissing('users', [
            'email' => 'student@test.com',
        ]);
    }

    /**
     * Test that a user cannot register with an invalid role.
     */
    public function test_user_cannot_register_with_invalid_role()
    {
        $response = $this->postJson('/register', [
            'name' => 'Fake Test',
            'email' => 'fake@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'superuser',
            'terms' => 'on',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
        $this->assertDatabaseMissing('users', [
            'email' => 'fake@test.com',
        ]);
    }

    /**
     * Test that a user cannot register without accepting terms.
     */
    public function test_user_cannot_register_without_accepting_terms()
    {
        $response = $this->postJson('/register', [
            'name' => 'No Terms Test',
            'email' => 'noterms@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'administrator',
            // 'terms' => 'on', // Omitted
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['terms']);
    }
}
