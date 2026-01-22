<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\VacationBalance;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('renders the employee overview page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $employee = Employee::factory()
            ->for($this->organization)
            ->for($this->employee)
            ->create();

        VacationBalance::factory()
            ->for($employee)
            ->for($this->organization)
            ->create();

        $response = $this->actingAs($this->employee)
            ->get(tenant_route('tenant.employee.overview', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('employee-overview/index')
                ->has('employee')
                ->has('vacationBalance')
                ->has('timeOffRequests')
            );
    });
