<?php

declare(strict_types=1);

namespace App\Actions\Employee;

use App\Data\PeopleDear\Employee\CreateEmployeeData;
use App\Models\Employee;
use App\Models\Organization;

final readonly class CreateEmployee
{
    /**
     * Create an employee for the given organization.
     */
    public function handle(CreateEmployeeData $data, Organization $organization): Employee
    {
        /** @var Employee $employee */
        $employee = Employee::query()->create([
            'organization_id' => $organization->id,
            ...$data->toArray(),
        ]);

        return $employee;
    }
}
