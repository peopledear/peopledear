<?php

declare(strict_types=1);

namespace App\Actions\Employee;

use App\Models\Employee;

final readonly class DeleteEmployee
{
    /**
     * Delete an employee.
     */
    public function handle(Employee $employee): void
    {
        $employee->delete();
    }
}
