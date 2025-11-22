<?php

declare(strict_types=1);

use App\Actions\Approval\CancelRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\TimeOffRequest;

test('cancels pending request', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->status)
        ->toBe(RequestStatus::Cancelled);
});

test('does not set approved_by on cancellation', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->approved_by)
        ->toBeNull()
        ->and($result->approved_at)
        ->toBeNull();
});

test('preserves original request data on cancellation', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->approvable_type)
        ->toBe(TimeOffRequest::class)
        ->and($result->approvable_id)
        ->toBe($timeOffRequest->id);
});
