<?php

declare(strict_types=1);

use App\Actions\Approval\CreateApproval;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;

test('creates pending approval by default', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    $action = app(CreateApproval::class);
    $approval = $action->handle($timeOffRequest, $timeOffRequest->organization_id);

    expect($approval->status)->toBe(RequestStatus::Pending)
        ->and($approval->approved_at)->toBeNull()
        ->and($approval->approvable_type)->toBe(TimeOffRequest::class)
        ->and($approval->approvable_id)->toBe($timeOffRequest->id)
        ->and($approval->organization_id)->toBe($timeOffRequest->organization_id);
});

test('creates approved approval with timestamp', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    $action = app(CreateApproval::class);
    $approval = $action->handle($timeOffRequest, $timeOffRequest->organization_id, RequestStatus::Approved);

    expect($approval->status)->toBe(RequestStatus::Approved)
        ->and($approval->approved_at)->not->toBeNull();
});

test('creates approval for different organization', function (): void {
    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()->createQuietly();

    /** @var App\Models\Organization $otherOrganization */
    $otherOrganization = App\Models\Organization::factory()->createQuietly();

    $action = app(CreateApproval::class);
    $approval = $action->handle($timeOffRequest, $otherOrganization->id);

    expect($approval->organization_id)->toBe($otherOrganization->id);
});
