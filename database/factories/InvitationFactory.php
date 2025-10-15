<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
final class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'role_id' => Role::factory(),
            'invited_by' => User::factory(),
            'token' => Str::uuid()->toString(),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }

    /**
     * Indicate that the invitation is expired.
     */
    public function expired(): self
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the invitation has been accepted.
     */
    public function accepted(): self
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now(),
        ]);
    }

    /**
     * Indicate that the invitation is pending.
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);
    }
}
