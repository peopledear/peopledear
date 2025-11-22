<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\VacationBalance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

final class VacationBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Collection<int, Employee> $employees */
        $employees = Employee::query()->get();

        /** @var Organization $organization */
        $organization = Organization::query()
            ->first();

        $employees->each(function (Employee $employee) use ($organization): void {

            VacationBalance::factory()
                ->for($employee)
                ->for($organization)
                ->createQuietly();

        });
    }
}
