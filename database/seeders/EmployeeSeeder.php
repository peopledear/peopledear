<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
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
        $organization = Organization::query()->first();

        $users = User::query()->get();

        $users->each(static function (User $user) use ($organization): void {

            Employee::factory()
                ->for($organization)
                ->for($user)
                ->for($organization->offices()->first())
                ->create([
                    'name' => $user->name,
                ]);

        });

    }
}
