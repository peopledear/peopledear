<?php

declare(strict_types=1);

use App\Actions\Approval\ApproveRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\VacationBalance;

test('updates time off request status to approved', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($organization)
        ->for($employee)
        ->createQuietly([
            'type' => TimeOffType::PersonalDay,
            'status' => RequestStatus::Pending,
        ]);

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($organization)
        ->createQuietly();

    $action = app(ApproveRequest::class);
    $action->handle($approval, $approver);

    expect($timeOffRequest->refresh()->status)
        ->toBe(RequestStatus::Approved);
});

test('deducts vacation balance when approving vacation request', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->createQuietly();

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->createQuietly([
        'organization_id' => $organization->id,
        'employee_id' => $employee->id,
        'year' => now()->year,
        'from_last_year' => 0,
        'accrued' => 2000, // 20 days
        'taken' => 500, // 5 days
    ]);

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($organization)
        ->for($employee)
        ->createQuietly([
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3),
            'is_half_day' => false,
        ]);

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($organization)
        ->createQuietly();

    $action = app(ApproveRequest::class);
    $action->handle($approval, $approver);

    expect($timeOffRequest->refresh()->status)
        ->toBe(RequestStatus::Approved)
        ->and($balance->refresh()->taken)
        ->toBe(800); // 5 + 3 days = 800
});

test('approves pending request', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->createQuietly();

    $action = app(ApproveRequest::class);
    $result = $action->handle($approval, $approver);

    expect($result->status)
        ->toBe(RequestStatus::Approved)
        ->and($result->approved_by)
        ->toBe($approver->id)
        ->and($result->approved_at)
        ->not->toBeNull();
});

test('sets approved_at timestamp', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->createQuietly();

    $action = app(ApproveRequest::class);
    $result = $action->handle($approval, $approver);

    expect($result->approved_at->diffInSeconds(now()))
        ->toBeLessThan(5);
});

test('records approver employee', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->createQuietly();

    $action = app(ApproveRequest::class);
    $result = $action->handle($approval, $approver);

    $result->load('approver');

    expect($result->approver)
        ->toBeInstanceOf(Employee::class)
        ->and($result->approver->id)
        ->toBe($approver->id);
});
