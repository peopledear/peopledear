<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\User;
use App\Queries\LatestUserTimeOffRequestsQuery;

test('returns latest time off requests for current user', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    TimeOffRequest::factory()
        ->for($employee)
        ->for($organization)
        ->count(3)
        ->createQuietly();

    $query = new LatestUserTimeOffRequestsQuery($user);

    $results = $query->builder()->get();

    expect($results)->toHaveCount(3);
});

test('limits results to default count of 5', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    TimeOffRequest::factory()
        ->for($employee)
        ->for($organization)
        ->count(10)
        ->createQuietly();

    $query = new LatestUserTimeOffRequestsQuery($user);

    $results = $query->builder()->get();

    expect($results)->toHaveCount(5);
});

test('count method changes the limit', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    TimeOffRequest::factory()
        ->for($employee)
        ->for($organization)
        ->count(10)
        ->createQuietly();

    $query = new LatestUserTimeOffRequestsQuery($user);

    $results = $query->count(3)->builder()->get();

    expect($results)->toHaveCount(3);
});

test('count method returns self for chaining', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    $query = new LatestUserTimeOffRequestsQuery($user);

    $result = $query->count(10);

    expect($result)->toBe($query);
});
