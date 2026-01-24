<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PeriodStatus;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

final class TimeOffRequestSeeder extends Seeder
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

            /** @var Period $activePeriod */
            $activePeriod = $organization->periods()
                ->where('status', PeriodStatus::Active)
                ->first();

            /** @var TimeOffType $vacationTimeOffType */
            $vacationTimeOffType = $organization->timeOffTypes()->first();

            TimeOffRequest::factory()
                ->for($employee)
                ->for($organization)
                ->for($activePeriod)
                ->for($vacationTimeOffType, 'type')
                ->count(3)
                ->createQuietly();

        });

    }
}
