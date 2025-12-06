<?php

declare(strict_types=1);

use App\Models\Approval;
use App\Models\Employee;
use App\Models\TimeOffRequest;
use App\Queries\PendingApprovalsQuery;

test('returns pending approvals for direct reports', function (): void {
    /** @var Employee $manager */
    $manager = Employee::factory()->create();

    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($manager->organization)
        ->withManager($manager)
        ->create();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($manager->organization)
        ->for($directReport)
        ->create();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($manager->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $query = app(PendingApprovalsQuery::class);
    $result = $query->builder($manager)->get();

    expect($result)
        ->toHaveCount(1)
        ->and($result->first()->id)
        ->toBe($approval->id);
});

test('excludes approved requests', function (): void {
    /** @var Employee $manager */
    $manager = Employee::factory()->create();

    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($manager->organization)
        ->withManager($manager)
        ->create();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($manager->organization)
        ->for($directReport)
        ->create();

    Approval::factory()
        ->approved()
        ->for($manager->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $query = app(PendingApprovalsQuery::class);
    $result = $query->builder($manager)->get();

    expect($result)->toHaveCount(0);
});

test('excludes requests from non-direct reports', function (): void {
    /** @var Employee $manager */
    $manager = Employee::factory()->create();

    /** @var Employee $otherEmployee */
    $otherEmployee = Employee::factory()
        ->for($manager->organization)
        ->create();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($manager->organization)
        ->for($otherEmployee)
        ->create();

    Approval::factory()
        ->pending()
        ->for($manager->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $query = app(PendingApprovalsQuery::class);
    $result = $query->builder($manager)->get();

    expect($result)->toHaveCount(0);
});

test('returns multiple pending approvals ordered by created_at', function (): void {
    /** @var Employee $manager */
    $manager = Employee::factory()->create();

    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($manager->organization)
        ->withManager($manager)
        ->create();

    /** @var TimeOffRequest $request1 */
    $request1 = TimeOffRequest::factory()
        ->for($manager->organization)
        ->for($directReport)
        ->create();

    /** @var TimeOffRequest $request2 */
    $request2 = TimeOffRequest::factory()
        ->for($manager->organization)
        ->for($directReport)
        ->create();

    /** @var Approval $approval1 */
    $approval1 = Approval::factory()
        ->pending()
        ->for($manager->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $request1->id,
            'created_at' => now()->subDay(),
        ]);

    /** @var Approval $approval2 */
    $approval2 = Approval::factory()
        ->pending()
        ->for($manager->organization)
        ->create([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $request2->id,
            'created_at' => now(),
        ]);

    $query = app(PendingApprovalsQuery::class);
    $result = $query->builder($manager)->get();

    expect($result)
        ->toHaveCount(2)
        ->and($result->first()->id)
        ->toBe($approval1->id)
        ->and($result->last()->id)
        ->toBe($approval2->id);
});
