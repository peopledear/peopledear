<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CrossDomainAuthToken;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CrossDomainAuthToken>
 */
final class CrossDomainAuthTokenFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'nonce' => Str::uuid()->toString(),
            'intended' => '/dashboard',
            'expires_at' => now()->addMinutes(5),
            'used_at' => null,
        ];
    }

    public function expired(): self
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subMinute(),
        ]);
    }

    public function used(): self
    {
        return $this->state(fn (array $attributes): array => [
            'used_at' => now(),
        ]);
    }
}
