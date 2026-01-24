<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CountrySubdivisionType;
use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Database\Eloquent\Factories\Factory;

use function fake;

/**
 * @extends Factory<CountrySubdivision>
 */
final class CountrySubdivisionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->city();

        return [
            'country_id' => Country::factory(),
            'country_subdivision_id' => null,
            'name' => [
                'EN' => $name,
                'DE' => $name,
                'FR' => $name,
            ],
            'code' => mb_strtoupper(fake()->unique()->bothify('??-##')),
            'iso_code' => mb_strtoupper(fake()->unique()->bothify('??-???')),
            'short_name' => mb_strtoupper(fake()->bothify('??')),
            'type' => fake()->randomElement(CountrySubdivisionType::cases()),
            'official_languages' => [
                mb_strtoupper(fake()->languageCode()),
            ],
        ];
    }
}
