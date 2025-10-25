<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Role $employeeRole */
        $employeeRole = Role::query()
            ->where('name', 'employee')
            ->first();
        /** @var Role $managerRole */
        $managerRole = Role::query()
            ->where('name', 'manager')
            ->first();
        /** @var Role $peopleManagerRole */
        $peopleManagerRole = Role::query()
            ->where('name', 'people_manager')
            ->first();
        /** @var Role $ownerRole */
        $ownerRole = Role::query()
            ->where('name', 'owner')
            ->first();

        /** @var User $employee */
        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@peopledear.test',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $employee->assignRole($employeeRole);

        /** @var User $manager */
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@peopledear.test',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $manager->assignRole($managerRole);

        /** @var User $peopleManager */
        $peopleManager = User::factory()->create([
            'name' => 'People Manager',
            'email' => 'peoplemanager@peopledear.test',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $peopleManager->assignRole($peopleManagerRole);

        /** @var User $owner */
        $owner = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@peopledear.test',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $owner->assignRole($ownerRole);
    }
}
