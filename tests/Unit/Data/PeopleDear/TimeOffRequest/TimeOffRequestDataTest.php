<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffRequest\TimeOffRequestData;
use App\Models\Organization;
use App\Models\Period;
use App\Models\TimeOffRequest;

test('create a time off request data', function (): void {

    $organization = Organization::factory()
        ->create();

    $timeOffRequest = TimeOffRequest::factory()
        ->for($organization)
        ->create();

    $period = Period::factory()
        ->for($organization)
        ->active()
        ->create();

    $data = TimeOffRequestData::from([
        'id' => $timeOffRequest->id,
        'organizationId' => $timeOffRequest->organization->id,
        'employeeId' => $timeOffRequest->employee->id,
        'period' => $period,
        'type' => $timeOffRequest->type,
        'status' => $timeOffRequest->status,
        'startDate' => $timeOffRequest->start_date,
        'endDate' => $timeOffRequest->end_date,
        'isHalfDay' => $timeOffRequest->is_half_day,
        'createdAt' => $timeOffRequest->created_at,
        'updatedAt' => $timeOffRequest->updated_at,
    ]);

    expect($data)
        ->toBeInstanceOf(TimeOffRequestData::class);

});
