<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
final class CountryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iso_code' => mb_strtoupper(fake()->unique()->bothify('??##')),
            'name' => [
                'EN' => fake()->country(),
                'DE' => fake()->country(),
                'FR' => fake()->country(),
            ],
            'official_languages' => [
                mb_strtoupper(fake()->languageCode()),
            ],
        ];
    }
}
