<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_theme_color()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('profile.update-theme'), [
            'theme_color' => 'assets/css/themes/elegance.min.css'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('assets/css/themes/elegance.min.css', $user->fresh()->theme_color);
    }

    public function test_user_can_update_theme_mode()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('profile.update-theme'), [
            'theme_mode' => 'on'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('on', $user->fresh()->theme_mode);
    }

    public function test_theme_preferences_are_rendered_in_head()
    {
        $user = User::factory()->create([
            'theme_color' => 'assets/css/themes/pulse.min.css',
            'theme_mode' => 'off'
        ]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('window.UserTheme = {', false);
        $response->assertSee('"assets/css/themes/pulse.min.css"', false);
        $response->assertSee('"off"', false);
    }
}
