<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

final class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::factory()->create([
            'iso_code' => 'US',
            'name' => ['en' => 'United States'],
            'official_languages' => ['en'],
        ]);

        Country::factory()->create([
            'iso_code' => 'GB',
            'name' => ['en' => 'United Kingdom'],
            'official_languages' => ['en'],
        ]);
    }
}
