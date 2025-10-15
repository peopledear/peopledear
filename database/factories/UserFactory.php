<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    private static ?string $password = null;

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
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): self
    {
        return $this->state(function (array $attributes): array {
            $adminRole = Role::query()
                ->where('name', 'admin')
                ->first();

            return [
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the user is a manager.
     */
    public function manager(): self
    {
        return $this->state(function (array $attributes): array {
            $managerRole = Role::query()
                ->where('name', 'manager')
                ->first();

            return [
                'role_id' => $managerRole?->id,
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the user is an employee.
     */
    public function employee(): self
    {
        return $this->state(function (array $attributes): array {
            $employeeRole = Role::query()
                ->where('name', 'employee')
                ->first();

            return [
                'role_id' => $employeeRole?->id,
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
