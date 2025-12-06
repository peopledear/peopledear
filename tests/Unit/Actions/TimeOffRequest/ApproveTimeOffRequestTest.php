<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\ApproveTimeOffRequest;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;

test('approves time off request', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->pending()
        ->create();

    $action = app(ApproveTimeOffRequest::class);
    $result = $action->handle($timeOffRequest);

    expect($result->status)
        ->toBe(RequestStatus::Approved);
});

test('returns refreshed model', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->pending()
        ->create();

    $action = app(ApproveTimeOffRequest::class);
    $result = $action->handle($timeOffRequest);

    expect($result->id)
        ->toBe($timeOffRequest->id);
});
