<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeOffRequest>
 */
final class TimeOffRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isHalfDay = fake()->boolean(20);
        $startDate = fake()->dateTimeBetween(now()->startOfYear(), now()->endOfYear());

        return [
            'organization_id' => Organization::factory(),
            'period_id' => Period::factory(),
            'employee_id' => Employee::factory(),
            'time_off_type_id' => TimeOffType::factory(),
            'status' => fake()->randomElement(RequestStatus::cases()),
            'start_date' => $startDate,
            'end_date' => fake()->optional(0.7)->dateTimeBetween($startDate, $startDate->add(DateInterval::createFromDateString('15 days'))),
            'is_half_day' => $isHalfDay,
        ];
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Pending,
        ]);
    }

    public function approved(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Approved,
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Rejected,
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RequestStatus::Cancelled,
        ]);
    }

    public function vacation(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => TimeOffType::Vacation,
        ]);
    }

    public function halfDay(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_half_day' => true,
            'end_date' => null,
        ]);
    }

    public function multiDay(): self
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 year');

        return $this->state(fn (array $attributes): array => [
            'is_half_day' => false,
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+2 years'),
        ]);
    }
}
