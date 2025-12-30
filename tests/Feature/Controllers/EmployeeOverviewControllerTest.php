<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\VacationBalance;

test('renders the employee overview page', function (): void {
    $employee = Employee::factory()
        ->for($this->organization)
        ->for($this->employee)
        ->create();

    VacationBalance::factory()
        ->for($employee)
        ->for($this->organization)
        ->create();

    $response = $this->actingAs($this->employee)
        ->get(route('employee.overview'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-overview/index')
            ->has('employee')
            ->has('vacationBalance')
            ->has('timeOffRequests')
        );
});
