<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'login' => fake()->userName(),
            'name' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'birth_date' => fake()->date('Y-m-d', '2005-01-01'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'member_type' => fake()->randomElement(['resident', 'worker', 'visitor', 'official']),
            'profile_picture' => null,
            'level' => fake()->randomElement(['débutant', 'intermédiaire', 'avancé', 'expert']),
            'points' => fake()->randomFloat(2, 0, 100),
            'is_admin' => 0,
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
}
