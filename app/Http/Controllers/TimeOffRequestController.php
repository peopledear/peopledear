<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Attributes\CurrentEmployee;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\TimeOffType;
use App\Http\Requests\StoreTimeOffRequest;
use App\Models\Employee;
use App\Queries\CurrentEmployeeQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class TimeOffRequestController
{
    public function create(CurrentEmployeeQuery $currentEmployeeQuery): Response
    {
        return Inertia::render('time-off/create', [
            'types' => TimeOffType::options(),
            'employee' => $currentEmployeeQuery->builder()
                ->with('organization')
                ->first(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(
        StoreTimeOffRequest $request,
        CreateTimeOffRequest $createTimeOff,
        #[CurrentEmployee] Employee $employee,
    ): RedirectResponse {

        $createTimeOff->handle(
            CreateTimeOffRequestData::from($request->validated()),
            $employee
        );

        return to_route('employee.overview')
            ->with('status', 'Time off request submitted successfully.');

    }
}
