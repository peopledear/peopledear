<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Seeder;

final class LocationSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Organization $organization */
        $organization = Organization::query()
            ->first();

        /** @var Country[] $countries */
        $countries = Country::query()->get();

        Location::factory()
            ->for($organization)
            ->for($countries[0])
            ->create([
                'name' => 'Warehouse East',
                'type' => LocationType::Warehouse,
            ]);

    }
}
