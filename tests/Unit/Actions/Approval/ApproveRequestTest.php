<?php

declare(strict_types=1);

use App\Actions\Approval\ApproveRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\TimeOffRequest;

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
