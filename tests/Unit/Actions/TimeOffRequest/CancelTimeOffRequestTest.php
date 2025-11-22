<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\CancelTimeOffRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;

test('cancels time off request', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->approved()
        ->createQuietly();

    $action = app(CancelTimeOffRequest::class);
    $result = $action->handle($timeOffRequest);

    expect($result->status)
        ->toBe(RequestStatus::Cancelled);
});

test('returns refreshed model', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->approved()
        ->createQuietly();

    $action = app(CancelTimeOffRequest::class);
    $result = $action->handle($timeOffRequest);

    expect($result->id)
        ->toBe($timeOffRequest->id);
});
