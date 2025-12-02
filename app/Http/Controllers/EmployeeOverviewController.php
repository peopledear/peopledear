<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Attributes\CurrentEmployee;
use App\Data\PeopleDear\TimeOffRequest\TimeOffRequestData;
use App\Data\PeopleDear\VacationBalance\VacationBalanceData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Queries\CurrentVacationBalanceQuery;
use App\Queries\LatestUserTimeOffRequestsQuery;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class EmployeeOverviewController
{
    public function index(
        #[CurrentEmployee] Employee $employee,
        LatestUserTimeOffRequestsQuery $latestUserTimeOffRequestsQuery,
        CurrentVacationBalanceQuery $currentVacationBalanceQuery,
    ): Response {

        $timeOffRequests = $latestUserTimeOffRequestsQuery
            ->builder()
            ->with(['employee', 'organization', 'period'])
            ->get();

        return Inertia::render(
            'employee-overview/index', [
                'employee' => $employee
                    ->load('user'),
                'vacationBalance' => VacationBalanceData::from(
                    $currentVacationBalanceQuery
                        ->builder()
                        ->first()
                ),
                'timeOffRequests' => TimeOffRequestData::collect(
                    $timeOffRequests,
                    Collection::class
                ),
                'types' => TimeOffType::options(),
                'statuses' => RequestStatus::options(),
            ]);
    }
}
