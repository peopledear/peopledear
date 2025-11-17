<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PeopleDear\OfficeType;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Database\Seeder;

final class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Organization $organization */
        $organization = Organization::query()
            ->first();

        /** @var Organization $secondOrganization */
        $secondOrganization = Organization::query()
            ->skip(1)
            ->first();

        Office::factory()->for($organization)
            ->create([
                'name' => 'Headquarters',
                'type' => OfficeType::Headquarters,
            ]);

        Office::factory()->for($secondOrganization)
            ->create([
                'name' => 'Headquarters',
                'type' => OfficeType::Headquarters,
            ]);

    }
}
