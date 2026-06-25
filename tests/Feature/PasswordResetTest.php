<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest can view forgot password page.
     */
    public function test_forgot_password_page_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Password?');
    }

    /**
     * Test that reset link can be requested.
     */
    public function test_reset_link_can_be_requested()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'user@test.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'user@test.com',
        ]);
    }

    /**
     * Test that reset password page can be rendered.
     */
    public function test_reset_password_page_can_be_rendered()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $token = Password::createToken($user);

        $response = $this->get("/reset-password/{$token}?email=user@test.com");

        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Password');
    }

    /**
     * Test that password can be reset with valid token.
     */
    public function test_password_can_be_reset_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'user@test.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
    }
}
