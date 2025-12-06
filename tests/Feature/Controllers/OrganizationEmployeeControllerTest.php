<?php

declare(strict_types=1);

use App\Actions\Organization\SetCurrentOrganization;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;

test('renders organization employees index',
    /**
     * @throws Throwable
     */
    function (): void {

        $user = User::factory()
            ->peopleManager()
            ->create();

        $organization = Organization::factory()->create();

        app(SetCurrentOrganization::class)
            ->handle($organization);

        $employees = Employee::factory()
            ->count(3)
            ->for($organization)
            ->create();

        $response = $this->actingAs($user)
            ->get(route('org.employees.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Inertia\Testing\AssertableInertia $page): Inertia\Testing\AssertableInertia => $page->component('org-employee/index')
            ->has('employees', 3)
            ->where('employees.0.id', $employees[0]->id)
            ->where('employees.1.id', $employees[1]->id)
            ->where('employees.2.id', $employees[2]->id)
        );
    });
