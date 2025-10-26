<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateEmployeeData;
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
