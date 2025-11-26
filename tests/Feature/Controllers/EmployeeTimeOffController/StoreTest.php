<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffType;
use App\Enums\PeopleDear\UserRole;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Models\VacationBalance;

beforeEach(function (): void {
    $this->organization = Organization::factory()->createQuietly();

    $this->user = User::factory()->createQuietly();

    $this->employee = Employee::factory()
        ->for($this->organization)
        ->for($this->user)
        ->createQuietly();

    $this->user->assignRole(UserRole::Employee);
});

test('can store a time off request', function (): void {
    VacationBalance::factory()->createQuietly([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => 2025,
        'from_last_year' => 0,
        'accrued' => 2000,
        'taken' => 0,
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('employee.time-offs.store'), [
            'employee_id' => $this->employee->id,
            'organization_id' => $this->organization->id,
            'type' => (string) TimeOffType::Vacation->value,
            'start_date' => '2025-01-15T00:00:00.000Z',
            'end_date' => '2025-01-17T00:00:00.000Z',
            'is_half_day' => false,
        ]);

    $response->assertRedirect(route('employee.overview'))
        ->assertSessionHas('status', 'Time off request submitted successfully.');

    $this->assertDatabaseHas('time_off_requests', [
        'employee_id' => $this->employee->id,
        'organization_id' => $this->organization->id,
        'type' => TimeOffType::Vacation->value,
    ]);
});

test('validates required fields when storing time off request', function (): void {
    $response = $this->actingAs($this->user)
        ->post(route('employee.time-offs.store'), []);

    $response->assertSessionHasErrors([
        'employee_id',
        'organization_id',
        'type',
        'start_date',
    ]);
});

test('validates type-specific rules with insufficient vacation balance', function (): void {
    VacationBalance::factory()->createQuietly([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => 2025,
        'from_last_year' => 0,
        'accrued' => 100,
        'taken' => 0,
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('employee.time-offs.store'), [
            'employee_id' => $this->employee->id,
            'organization_id' => $this->organization->id,
            'type' => (string) TimeOffType::Vacation->value,
            'start_date' => '2025-01-15T00:00:00.000Z',
            'end_date' => '2025-01-20T00:00:00.000Z',
            'is_half_day' => false,
        ]);

    $response->assertSessionHasErrors(['balance']);
});

test('validates end date must be after or equal to start date', function (): void {
    $response = $this->actingAs($this->user)
        ->post(route('employee.time-offs.store'), [
            'employee_id' => $this->employee->id,
            'organization_id' => $this->organization->id,
            'type' => (string) TimeOffType::Vacation->value,
            'start_date' => '2025-01-17',
            'end_date' => '2025-01-15',
            'is_half_day' => false,
        ]);

    $response->assertSessionHasErrors(['end_date']);
});

test('unauthenticated user cannot store time off request', function (): void {
    $response = $this->post(route('employee.time-offs.store'), []);

    $response->assertRedirect(route('login'));
});
