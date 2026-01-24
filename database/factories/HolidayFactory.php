<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\HolidayType;
use App\Models\Country;
use App\Models\Holiday;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Holiday>
 */
final class HolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'country_id' => Country::factory(),
            'date' => fake()->dateTimeBetween('now', '+1 year'),
            'name' => [
                'en' => fake()->words(3, true),
                'pt' => fake()->words(3, true),
            ],
            'type' => fake()->randomElement(HolidayType::cases()),
            'nationwide' => fake()->boolean(70),
            'subdivision_code' => fake()->optional()->regexify('[A-Z]{2}'),
            'api_holiday_id' => null,
            'is_custom' => false,
        ];
    }

    public function custom(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_custom' => true,
            'api_holiday_id' => null,
        ]);
    }

    public function fromApi(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_custom' => false,
            'api_holiday_id' => fake()->uuid(),
        ]);
    }

    public function nationwide(): self
    {
        return $this->state(fn (array $attributes): array => [
            'nationwide' => true,
            'subdivision_code' => null,
        ]);
    }

    public function regional(): self
    {
        return $this->state(fn (array $attributes): array => [
            'nationwide' => false,
            'subdivision_code' => fake()->regexify('[A-Z]{2}'),
        ]);
    }
}
