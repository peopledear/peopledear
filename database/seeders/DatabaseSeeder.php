<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            UserSeeder::class,
            PeriodSeeder::class,
            OfficeSeeder::class,
            EmployeeSeeder::class,
            TimeOffTypeSeeder::class,
            TimeOffRequestSeeder::class,
            VacationBalanceSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
