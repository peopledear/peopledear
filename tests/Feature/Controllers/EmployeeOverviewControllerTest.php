<?php

declare(strict_types=1);

use App\Enums\PeopleDear\UserRole;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Models\VacationBalance;

test('renders the employee overview page', function (): void {
    $organization = Organization::factory()
        ->create();

    $user = User::factory()
        ->create();

    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    VacationBalance::factory()
        ->for($employee)
        ->for($organization)
        ->create();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->get(route('employee.overview'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-overview/index')
            ->has('employee')
            ->has('vacationBalance')
            ->has('timeOffRequests')
        );
});
