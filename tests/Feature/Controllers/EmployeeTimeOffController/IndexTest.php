<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Enums\PeopleDear\UserRole;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\User;

beforeEach(function (): void {
    $this->organization = Organization::factory()->createQuietly();

    $this->user = User::factory()->createQuietly();

    $this->employee = Employee::factory()
        ->for($this->organization)
        ->for($this->user)
        ->createQuietly();

    $this->user->assignRole(UserRole::Employee);
});

test('authenticated employee can access time offs page', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests')
            ->has('types')
            ->has('statuses')
            ->has('filters')
        );
});

test('unauthenticated user is redirected to login', function (): void {
    $response = $this->get(route('employee.time-offs.index'));

    $response->assertRedirect(route('login'));
});

test('employee sees paginated time off requests with 20 per page', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->count(25)
        ->createQuietly();

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 20)
            ->where('timeOffRequests.per_page', 20)
            ->where('timeOffRequests.total', 25)
        );
});

test('page displays empty state when user has no requests', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 0)
        );
});

test('requests are ordered by created_at desc', function (): void {
    /** @var TimeOffRequest $oldRequest */
    $oldRequest = TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['created_at' => now()->subDays(2)]);

    /** @var TimeOffRequest $newRequest */
    $newRequest = TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['created_at' => now()]);

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->where('timeOffRequests.data.0.id', $newRequest->id)
            ->where('timeOffRequests.data.1.id', $oldRequest->id)
        );
});

test('filtering by status returns only matching requests', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['status' => RequestStatus::Pending]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['status' => RequestStatus::Approved]);

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index', ['status' => RequestStatus::Pending->value]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 1)
            ->where('timeOffRequests.data.0.status.status', RequestStatus::Pending->value)
        );
});

test('clearing status filter returns all requests', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['status' => RequestStatus::Pending]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['status' => RequestStatus::Approved]);

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 2)
        );
});

test('status filter persists in URL query params', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index', ['status' => RequestStatus::Pending->value]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->where('filters.status', RequestStatus::Pending->value)
        );
});

test('filtering by type returns only matching requests', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['type' => TimeOffType::Vacation]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly(['type' => TimeOffType::SickLeave]);

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index', ['type' => TimeOffType::Vacation->value]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 1)
            ->where('timeOffRequests.data.0.type.type', TimeOffType::Vacation->value)
        );
});

test('combined status and type filters return correct results', function (): void {
    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly([
            'status' => RequestStatus::Pending,
            'type' => TimeOffType::Vacation,
        ]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly([
            'status' => RequestStatus::Approved,
            'type' => TimeOffType::Vacation,
        ]);

    TimeOffRequest::factory()
        ->for($this->employee)
        ->for($this->organization)
        ->createQuietly([
            'status' => RequestStatus::Pending,
            'type' => TimeOffType::SickLeave,
        ]);

    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index', [
            'status' => RequestStatus::Pending->value,
            'type' => TimeOffType::Vacation->value,
        ]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->has('timeOffRequests.data', 1)
            ->where('timeOffRequests.data.0.status.status', RequestStatus::Pending->value)
            ->where('timeOffRequests.data.0.type.type', TimeOffType::Vacation->value)
        );
});

test('type filter persists in URL query params', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.index', ['type' => TimeOffType::Vacation->value]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/index')
            ->where('filters.type', TimeOffType::Vacation->value)
        );
});
