<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Throwable;

use function resolve;

abstract class TenantTestCase extends TestCase
{
    protected Organization $tenant;

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

        $this->tenant = Organization::factory()->create([
            'name' => 'Test Company',
            'identifier' => 'testcompany',
        ]);

        $this->employee = User::factory()
            ->for($this->tenant)
            ->create([
                'email' => 'employee@peopledear.test',
            ]);

        $this->peopleManager = User::factory()
            ->for($this->tenant)
            ->create([
                'email' => 'peoplemanager@peopledear.test',
            ]);

        $this->manager = User::factory()
            ->for($this->tenant)
            ->create([
                'email' => 'manager@peopledear.test',
            ]);

        $this->owner = User::factory()
            ->for($this->tenant)
            ->create([
                'email' => 'owner@peopledear.test',
            ]);

        $this->employee->assignRole(UserRole::Employee);
        $this->peopleManager->assignRole(UserRole::PeopleManager);
        $this->manager->assignRole(UserRole::Manager);
        $this->owner->assignRole(UserRole::Owner);

    }

    final public function actingAs(Authenticatable $user, $guard = null): self
    {
        Auth::login($user);

        return $this;
    }
}
