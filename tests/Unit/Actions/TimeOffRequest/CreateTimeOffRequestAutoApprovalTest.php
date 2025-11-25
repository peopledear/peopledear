<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;

test('sick leave is auto-approved', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    $data = CreateTimeOffRequestData::from([
        'organization_id' => $employee->organization_id,
        'employee_id' => $employee->id,
        'type' => TimeOffType::SickLeave,
        'start_date' => now()->addDays(1),
        'end_date' => now()->addDays(1),
        'is_half_day' => false,
    ]);

    $action = app(CreateTimeOffRequest::class);
    $result = $action->handle($data, $employee);

    $result->load('approval');

    expect($result->approval)
        ->not->toBeNull()
        ->and($result->approval->status)
        ->toBe(RequestStatus::Approved)
        ->and($result->approval->approved_at)
        ->not->toBeNull();
});

test('vacation requires approval', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    $data = CreateTimeOffRequestData::from([
        'organization_id' => $employee->organization_id,
        'employee_id' => $employee->id,
        'type' => TimeOffType::Vacation,
        'start_date' => now()->addDays(1),
        'end_date' => now()->addDays(3),
        'is_half_day' => false,
    ]);

    $action = app(CreateTimeOffRequest::class);
    $result = $action->handle($data, $employee);

    $result->load('approval');

    expect($result->approval)
        ->not->toBeNull()
        ->and($result->approval->status)
        ->toBe(RequestStatus::Pending)
        ->and($result->approval->approved_at)
        ->toBeNull();
});

test('personal day requires approval', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    $data = CreateTimeOffRequestData::from([
        'organization_id' => $employee->organization_id,
        'employee_id' => $employee->id,
        'type' => TimeOffType::PersonalDay,
        'start_date' => now()->addDays(1),
        'end_date' => now()->addDays(1),
        'is_half_day' => false,
    ]);

    $action = app(CreateTimeOffRequest::class);
    $result = $action->handle($data, $employee);

    $result->load('approval');

    expect($result->approval)
        ->not->toBeNull()
        ->and($result->approval->status)
        ->toBe(RequestStatus::Pending)
        ->and($result->approval->approved_at)
        ->toBeNull();
});
