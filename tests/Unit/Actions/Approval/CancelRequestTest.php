<?php

declare(strict_types=1);

use App\Actions\Approval\CancelRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\TimeOffRequest;

test('reverses processor when cancelling approved vacation request', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->vacation()
        ->create([
            'start_date' => now(),
            'end_date' => null,
            'is_half_day' => false,
        ]);

    /** @var App\Models\VacationBalance $balance */
    $balance = App\Models\VacationBalance::factory()->create([
        'employee_id' => $timeOffRequest->employee_id,
        'year' => $timeOffRequest->start_date->year,
        'accrued' => 2500,
        'taken' => 100,
    ]);

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->approved()
        ->for($timeOffRequest->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $action = app(CancelRequest::class);
    $action->handle($approval);

    $balance->refresh();
    expect($balance->taken)->toBe(0);
});

test('cancels pending request', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->status)
        ->toBe(RequestStatus::Cancelled);
});

test('does not set approved_by on cancellation', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->approved_by)
        ->toBeNull()
        ->and($result->approved_at)
        ->toBeNull();
});

test('preserves original request data on cancellation', function (): void {
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

    $action = app(CancelRequest::class);
    $result = $action->handle($approval);

    expect($result->approvable_type)
        ->toBe(TimeOffRequest::class)
        ->and($result->approvable_id)
        ->toBe($timeOffRequest->id);
});
