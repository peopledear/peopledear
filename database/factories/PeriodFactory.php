<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Period>
 */
final class PeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = now();

        return [
            'organization_id' => Organization::factory(),
            'start' => $now->startOfYear(),
            'end' => $now->endOfYear(),
            'year' => $now->year,
            'status' => fake()->randomElement(PeriodStatus::cases()),
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => [
            'status' => PeriodStatus::Active,
        ]);
    }

    public function closed(): self
    {
        return $this->state(fn (): array => [
            'status' => PeriodStatus::Closed,
        ]);
    }
}
