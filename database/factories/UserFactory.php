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
            'nik' => 'PKL-'.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'karyawan',
            'device_id' => 'dev-'.Str::random(10),
            'is_active' => true,
            'division' => 'Divisi IT Development',
            'shift_id' => null,
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

    public function manager(): static
    {
        return $this->state(fn () => [
            'role' => 'manajer',
            'nik' => 'MGR-'.fake()->unique()->numerify('####'),
            'division' => 'Manajer Operasional & HRD',
        ]);
    }
}
