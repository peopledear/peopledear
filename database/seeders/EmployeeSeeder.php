<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

final class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Organization $organization */
        $organization = Organization::query()->first();

        $users = User::query()->get();

        $this->createEmployeesWithHierarchy($organization, $users);
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function createEmployeesWithHierarchy(Organization $organization, $users): void
    {
        /** @var Location $headQuarter */
        $headQuarter = $organization->headOffice()->first();

        $employees = [];

        foreach ($users as $index => $user) {
            $factory = Employee::factory()
                ->for($organization)
                ->for($user)
                ->for($headQuarter);

            // First employee is the owner (no manager)
            // Second employee reports to owner
            // Remaining employees report to second employee (department manager)
            if ($index === 1 && isset($employees[0])) {
                $factory = $factory->withManager($employees[0]);
            } elseif ($index > 1 && isset($employees[1])) {
                $factory = $factory->withManager($employees[1]);
            }

            /** @var Employee $employee */
            $employee = $factory->create([
                'name' => $user->name,
            ]);

            $employees[] = $employee;
        }
    }
}
