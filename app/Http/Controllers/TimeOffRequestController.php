<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\TimeOffType;
use App\Http\Requests\StoreTimeOffRequest;
use App\Queries\CurrentEmployeeQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

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

    public function store(StoreTimeOffRequest $request, CreateTimeOffRequest $createTimeOff): RedirectResponse
    {

        $createTimeOff->handle(
            CreateTimeOffRequestData::from($request->validated())
        );

        return to_route('employee.overview')
            ->with('status', 'Time off request submitted successfully.');

    }
}
