<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Throwable;

final class UserSeeder extends Seeder
{
    /**
     * Sequence of users with proper names, test emails, and their assigned roles.
     *
     * @var array<int, array{name: string, email: string, role: string}>
     */
    private const array USERS = [
        [
            'name' => 'Emily Thompson',
            'email' => 'employee@peopledear.test',
            'role' => UserRole::Employee->value,
        ],
        [
            'name' => 'Michael Rodriguez',
            'email' => 'manager@peopledear.test',
            'role' => UserRole::Manager->value,
        ],
        [
            'name' => 'Sarah Chen',
            'email' => 'peoplemanager@peopledear.test',
            'role' => UserRole::PeopleManager->value,
        ],
        [
            'name' => 'James Wilson',
            'email' => 'owner@peopledear.test',
            'role' => UserRole::Owner->value,
        ],
    ];

    public function __construct(private readonly CreateSystemRoles $createSystemRoles) {}

    /**
     * @throws Throwable
     */
    public function run(): void
    {

        $this->createSystemRoles->handle();

        foreach (self::USERS as $userData) {
            /** @var Role $role */
            $role = Role::query()
                ->where('name', $userData['role'])
                ->first();

            /** @var User $user */
            $user = User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ]);

            $user->assignRole($role);
        }
    }
}
