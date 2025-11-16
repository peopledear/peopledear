<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use Inertia\Inertia;
use Inertia\Response;

final class OrganizationEmployeeController
{
    public function index(): Response
    {

        Employee::query()
            ->whereHas('organization', function ($query): void {
                $query->where('id', 1);
            })->get();

        return Inertia::render('org-employee/index', []);

    }
}
