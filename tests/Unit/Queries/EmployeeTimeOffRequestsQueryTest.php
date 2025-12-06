<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\User;
use App\Queries\EmployeeTimeOffRequestsQuery;

beforeEach(function (): void {
    $this->organization = Organization::factory()->create();

    $this->user = User::factory()->create();

    $this->employee = Employee::factory()
        ->for($this->organization)
        ->for($this->user)
        ->create();

    $this->actingAs($this->user);

    $this->query = app(EmployeeTimeOffRequestsQuery::class);
});

test('returns time off requests for current user ordered by created_at desc', function (): void {
    /** @var TimeOffRequest $oldRequest */
    $oldRequest = TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['created_at' => now()->subDays(2)]);

    /** @var TimeOffRequest $newRequest */
    $newRequest = TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['created_at' => now()]);

    $results = $this->query->builder()->get();

    expect($results)->toHaveCount(2)
        ->and($results->first()->id)->toBe($newRequest->id)
        ->and($results->last()->id)->toBe($oldRequest->id);
});

test('does not return requests from other employees', function (): void {
    /** @var User $otherUser */
    $otherUser = User::factory()->create();

    /** @var Employee $otherEmployee */
    $otherEmployee = Employee::factory()
        ->for($this->organization)
        ->for($otherUser)
        ->create();

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create();

    TimeOffRequest::factory()
        ->for($otherEmployee)
        ->for($this->organization)
        ->create();

    $results = $this->query->builder()->get();

    expect($results)->toHaveCount(1);
});

test('withStatus filters by RequestStatus enum value', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['status' => RequestStatus::Pending]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['status' => RequestStatus::Approved]);

    $results = $this->query
        ->withStatus(RequestStatus::Pending->value)
        ->builder()
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->status)->toBe(RequestStatus::Pending);
});

test('withStatus returns self for chaining', function (): void {
    $result = $this->query->withStatus(RequestStatus::Pending->value);

    expect($result)->toBe($this->query);
});

test('withType filters by TimeOffType enum value', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['type' => TimeOffType::Vacation]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create(['type' => TimeOffType::SickLeave]);

    $results = $this->query
        ->withType(TimeOffType::Vacation->value)
        ->builder()
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->type)->toBe(TimeOffType::Vacation);
});

test('withType returns self for chaining', function (): void {
    $result = $this->query->withType(TimeOffType::Vacation->value);

    expect($result)->toBe($this->query);
});

test('combined status and type filters return correct results', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create([
            'status' => RequestStatus::Pending,
            'type' => TimeOffType::Vacation,
        ]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create([
            'status' => RequestStatus::Approved,
            'type' => TimeOffType::Vacation,
        ]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->create([
            'status' => RequestStatus::Pending,
            'type' => TimeOffType::SickLeave,
        ]);

    $results = $this->query
        ->withStatus(RequestStatus::Pending->value)
        ->withType(TimeOffType::Vacation->value)
        ->builder()
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->status)->toBe(RequestStatus::Pending)
        ->and($results->first()->type)->toBe(TimeOffType::Vacation);
});

test('null status does not apply filter', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->count(3)
        ->create();

    $results = $this->query
        ->withStatus(null)
        ->builder()
        ->get();

    expect($results)->toHaveCount(3);
});

test('null type does not apply filter', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->count(3)
        ->create();

    $results = $this->query
        ->withType(null)
        ->builder()
        ->get();

    expect($results)->toHaveCount(3);
});
