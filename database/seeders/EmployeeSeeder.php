<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

final class EmployeeSeeder extends Seeder
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
            ->skip(1)->first();

        $users = User::query()->get();

        $users->each(static function (User $user) use ($organization, $secondOrganization): void {

            /** @var Office $office */
            $office = $organization->offices()->first();

            Employee::factory()
                ->for($organization)
                ->for($user)
                ->for($office)
                ->create([
                    'name' => $user->name,
                ]);

            /** @var Office $secondOffice */
            $secondOffice = $secondOrganization
                ->offices()
                ->first();

            Employee::factory()
                ->for($secondOrganization)
                ->for($user)
                ->for($secondOffice)
                ->create([
                    'name' => $user->name,
                ]);

        });

    }
}
