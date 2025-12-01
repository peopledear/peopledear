<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Seeder;

final class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        $organizations->each(static function (Organization $organization): void {

            Period::factory()
                ->for($organization)
                ->active()
                ->createQuietly();

        });

    }
}
