<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;

use function App\organization;

final class OrganizationEmployeeController
{
    public function index(): Response
    {

        /** @var Collection<int, Employee> $employees */
        $employees = Employee::query()
            ->whereHas('organization', function (Builder $query): void {
                $query->where('id', organization()?->id);
            })->get();

        return Inertia::render('org-employee/index', [
            'employees' => $employees,
        ]);

    }
}
