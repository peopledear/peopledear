<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\TimeOffBalanceMode;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Enums\Support\TimeOffIcon;
use App\Models\Organization;
use App\Models\TimeOffType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

use function fake;

/**
 * @extends Factory<TimeOffType>
 */
final class TimeOffTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'is_system' => fake()->boolean(10),
            'allowed_units' => fake()->randomElements(TimeOffUnit::cases()),
            'icon' => fake()->randomElement(TimeOffIcon::cases()),
            'color' => fake()->hexColor(),
            'is_active' => fake()->boolean(90),
            'requires_approval' => fake()->boolean(90),
            'requires_justification' => fake()->boolean(10),
            'requires_justification_document' => fake()->boolean(10),
            'balance_mode' => fake()->randomElement(TimeOffBalanceMode::cases()),
        ];
    }

    public function withFallbackApprovalRole(): self
    {

        $role = Role::findByName(\App\Enums\PeopleDear\Role::Owner->value);

        return $this->state(fn (array $attributes): array => [
            'fallback_approval_role_id' => $role->id,
        ]);
    }

    public function isSystem(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_system' => true,
        ]);
    }

    public function requiresApproval(): self
    {
        return $this->state(fn (array $attributes): array => [
            'requires_approval' => true,
        ]);
    }

    public function requiresJustification(): self
    {
        return $this->state(fn (array $attributes): array => [
            'requires_justification' => true,
        ]);
    }
}
