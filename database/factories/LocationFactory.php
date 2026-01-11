<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PeopleDear\LocationType;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
final class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'country_id' => Country::factory(),
            'name' => fake()->company().' Location',
            'type' => fake()->randomElement(LocationType::cases()),
            'phone' => fake()->optional()->phoneNumber(),
        ];
    }
}
