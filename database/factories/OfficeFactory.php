<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OfficeType;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
final class OfficeFactory extends Factory
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
            'name' => fake()->company().' Office',
            'type' => fake()->randomElement(OfficeType::cases()),
            'phone' => fake()->optional()->phoneNumber(),
        ];
    }
}
