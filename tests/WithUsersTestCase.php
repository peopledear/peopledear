<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Throwable;

abstract class WithUsersTestCase extends TestCase
{
    protected Organization $organization;

    protected User $employee;

    protected User $peopleManager;

    protected User $manager;

    protected User $owner;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        resolve(CreateSystemRoles::class)->handle();

        $this->organization = Organization::factory()->create();

        $this->employee = User::factory()->create([
            'email' => 'employee@peopledear.test',
        ]);

        $this->peopleManager = User::factory()->create([
            'email' => 'peoplemanager@peopledear.test',
        ]);

        $this->manager = User::factory()->create([
            'email' => 'manager@peopledear.test',
        ]);

        $this->owner = User::factory()->create([
            'email' => 'owner@peopledear.test',
        ]);

        $this->employee->assignRole(UserRole::Employee);
        $this->peopleManager->assignRole(UserRole::PeopleManager);
        $this->manager->assignRole(UserRole::Manager);
        $this->owner->assignRole(UserRole::Owner);

    }
}
