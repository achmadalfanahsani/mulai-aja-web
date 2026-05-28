<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State for student role
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STUDENT,
        ]);
    }

    /**
     * State for teacher role
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_TEACHER,
        ]);
    }

    /**
     * State for administrator role
     */
    public function administrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMINISTRATOR,
        ]);
    }

    /**
     * State for superuser role
     */
    public function superuser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_SUPERUSER,
        ]);
    }

    /**
     * State for pending approval
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    /**
     * State for approved
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * State for administrator pending approval
     * @deprecated Use administrator()->unapproved() instead
     */
    public function pendingAdministrator(): static
    {
        return $this->administrator()->unapproved();
    }
}
