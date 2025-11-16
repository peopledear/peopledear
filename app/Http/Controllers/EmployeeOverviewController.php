<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\CurrentEmployeeQuery;
use Inertia\Inertia;
use Inertia\Response;

final class EmployeeOverviewController
{
    public function index(CurrentEmployeeQuery $employeeByUserIdQuery): Response
    {
        return Inertia::render(
            'employee-overview/index', [
                'employee' => $employeeByUserIdQuery->builder()->first(),
            ]);
    }
}
