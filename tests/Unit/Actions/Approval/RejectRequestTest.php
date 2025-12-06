<?php

declare(strict_types=1);

use App\Actions\Approval\RejectRequest;
use App\Data\PeopleDear\Approval\RejectRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\TimeOffRequest;

test('rejects pending request', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->create();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->create();

    $data = RejectRequestData::from([
        'rejection_reason' => 'Team is understaffed during this period',
    ]);

    $action = app(RejectRequest::class);
    $result = $action->handle($approval, $approver, $data);

    expect($result->status)
        ->toBe(RequestStatus::Rejected)
        ->and($result->approved_by)
        ->toBe($approver->id)
        ->and($result->approved_at)
        ->not->toBeNull()
        ->and($result->rejection_reason)
        ->toBe('Team is understaffed during this period');
});

test('sets rejected_at timestamp', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->create();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->create();

    $data = RejectRequestData::from([
        'rejection_reason' => 'Not approved',
    ]);

    $action = app(RejectRequest::class);
    $result = $action->handle($approval, $approver, $data);

    expect($result->approved_at->diffInSeconds(now()))
        ->toBeLessThan(5);
});

test('records rejection reason', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->create();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->create();

    $reason = 'Project deadline next week, please reschedule';

    $data = RejectRequestData::from([
        'rejection_reason' => $reason,
    ]);

    $action = app(RejectRequest::class);
    $result = $action->handle($approval, $approver, $data);

    expect($result->rejection_reason)
        ->toBe($reason);
});

test('records approver employee on rejection', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->create();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($timeOffRequest->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    /** @var Employee $approver */
    $approver = Employee::factory()
        ->for($timeOffRequest->organization)
        ->create();

    $data = RejectRequestData::from([
        'rejection_reason' => 'Denied',
    ]);

    $action = app(RejectRequest::class);
    $result = $action->handle($approval, $approver, $data);

    $result->load('approver');

    expect($result->approver)
        ->toBeInstanceOf(Employee::class)
        ->and($result->approver->id)
        ->toBe($approver->id);
});
