<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\TimeOffTypeStatus;
use App\Enums\TimeOffUnit;
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
            'icon' => fake()->randomElement(Icon::cases()),
            'color' => fake()->hexColor(),
            'status' => fake()->randomElement([
                TimeOffTypeStatus::Active,     // 70% chance
                TimeOffTypeStatus::Pending,    // 20% chance
                TimeOffTypeStatus::Inactive,   // 10% chance
            ]),
            'requires_approval' => fake()->boolean(90),
            'requires_justification' => fake()->boolean(10),
            'requires_justification_document' => fake()->boolean(10),
            'balance_mode' => fake()->randomElement(BalanceType::cases()),
            'balance_config' => [
                'accrual_days_per_year' => fake()->numberBetween(20, 30),
                'carry_over_type' => 1,
                'carry_over_days_limit' => 5,
            ],
        ];
    }

    public function withFallbackApprovalRole(): self
    {

        $role = Role::findByName(\App\Enums\UserRole::Owner->value);

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

    public function dontRequireApproval(): self
    {
        return $this->state(fn (array $attributes): array => [
            'requires_approval' => false,
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

    public function active(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffTypeStatus::Active,
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffTypeStatus::Pending,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffTypeStatus::Inactive,
        ]);
    }
}
