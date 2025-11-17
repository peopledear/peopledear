<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\TimeOffStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeOff>
 */
final class TimeOffFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isHalfDay = fake()->boolean(20);
        $startDate = fake()->dateTimeBetween('now', '+1 year');

        return [
            'organization_id' => Organization::factory(),
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(TimeOffType::cases()),
            'status' => fake()->randomElement(TimeOffStatus::cases()),
            'start_date' => $startDate,
            'end_date' => $isHalfDay ? null : fake()->optional(0.7)->dateTimeBetween($startDate, '+2 years'),
            'is_half_day' => $isHalfDay,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffStatus::Pending,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffStatus::Approved,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffStatus::Rejected,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TimeOffStatus::Cancelled,
        ]);
    }

    public function halfDay(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_half_day' => true,
            'end_date' => null,
        ]);
    }

    public function multiDay(): static
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 year');

        return $this->state(fn (array $attributes): array => [
            'is_half_day' => false,
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+2 years'),
        ]);
    }
}
