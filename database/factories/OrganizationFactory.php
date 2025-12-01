<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'vat_number' => fake()->numerify('##########'),
            'ssn' => fake()->numerify('##-#######'),
            'phone' => fake()->phoneNumber(),
        ];
    }

    public function withActivePeriod(): self
    {
        return $this->afterCreating(function (Organization $organization): void {
            Period::factory()
                ->for($organization)
                ->active()
                ->createQuietly();
        });
    }
}
