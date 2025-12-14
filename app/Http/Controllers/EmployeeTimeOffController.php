<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\TimeOffRequest\CreateTimeOffRequest as CreateTimeOffRequestAction;
use App\Attributes\CurrentEmployee;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Data\PeopleDear\TimeOffRequest\TimeOffRequestData;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeData;
use App\Enums\PeopleDear\RequestStatus;
use App\Http\Requests\CreateTimeOffRequest;
use App\Models\Employee;
use App\Models\Period;
use App\Queries\CurrentEmployeeQuery;
use App\Queries\EmployeeTimeOffRequestsQuery;
use App\Queries\PeriodQuery;
use App\Queries\TimeOffTypeQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class EmployeeTimeOffController
{
    public function index(
        Request $request,
        EmployeeTimeOffRequestsQuery $query,
    ): Response {
        $status = $request->has('status')
            ? $request->integer('status')
            : null;

        $type = $request->has('type')
            ? $request->integer('type')
            : null;

        $timeOffRequests = $query
            ->withStatus($status)
            ->withType($type)
            ->builder()
            ->with('employee', 'organization', 'period')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('employee-time-offs/index', [
            'timeOffRequests' => TimeOffRequestData::collect($timeOffRequests),
            'statuses' => RequestStatus::options(),
            'filters' => [
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    public function create(
        CurrentEmployeeQuery $currentEmployeeQuery,
        PeriodQuery $periodQuery,
        TimeOffTypeQuery $timeOffTypeQuery,
    ): Response {
        /** @var Period $period */
        $period = $periodQuery()
            ->active()
            ->first();

        $timeOffTypes = $timeOffTypeQuery()
            ->active()
            ->get();

        return Inertia::render('employee-time-offs/create', [
            'timeOffTypes' => TimeOffTypeData::collect(
                $timeOffTypes,
                Collection::class
            ),
            'period' => $period,
            'employee' => $currentEmployeeQuery->builder()
                ->with('organization')
                ->first(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(
        CreateTimeOffRequest $request,
        CreateTimeOffRequestAction $createTimeOff,
        TimeOffTypeQuery $timeOffTypeQuery,
        #[CurrentEmployee] Employee $employee,
    ): RedirectResponse {

        $timeOffType = $timeOffTypeQuery()
            ->make()
            ->find($request->string('time_off_type_id'));

        $createTimeOff->handle(
            CreateTimeOffRequestData::from([
                ...$request->validated(),
            ]),
            $employee,
            $timeOffType,
        );

        return to_route('employee.overview')
            ->with('status', 'Time off request submitted successfully.');
    }
}
