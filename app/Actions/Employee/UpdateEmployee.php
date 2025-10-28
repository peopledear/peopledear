<?php

declare(strict_types=1);

namespace App\Actions\Employee;

use App\Data\PeopleDear\Employee\UpdateEmployeeData;
use App\Models\Employee;

final readonly class UpdateEmployee
{
    /**
     * Update an employee.
     */
    public function handle(Employee $employee, UpdateEmployeeData $data): Employee
    {
        $employee->update($data->toArray());

        return $employee->refresh();
    }
}
