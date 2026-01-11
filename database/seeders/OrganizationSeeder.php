<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PeopleDear\LocationType;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Seeder;

final class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Organization $organization */
        $organization = Organization::factory()->create([
            'name' => 'PeopleDear Inc.',
            'identifier' => 'peopledear-inc',
            'vat_number' => 'VAT123456789',
            'ssn' => 'SSN987654321',
            'phone' => '+1-555-0100',
        ]);

        /** @var Country $country */
        $country = Country::query()
            ->first();

        /** @var Location $headquarters */
        $headquarters = Location::factory()
            ->for($organization)
            ->for($country)
            ->create([
                'name' => 'Headquarters',
                'type' => LocationType::Headquarters,
                'phone' => '+1-555-0101',
            ]);

        $headquarters->address()->create([
            'line1' => '123 Business Ave',
            'line2' => 'Suite 100',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'United States',
        ]);
    }
}
