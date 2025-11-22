<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffType;
use App\Enums\PeopleDear\UserRole;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;

test('renders the create time off request page', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    /** @var User $user */
    $user = User::factory()
        ->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->get(route('employee.time-off.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('time-off/create')
            ->has('employee')
            ->has('types')
        );
});

test('can store a time off request', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    /** @var User $user */
    $user = User::factory()
        ->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->post(route('employee.time-off.store'), [
            'employee_id' => $employee->id,
            'organization_id' => $organization->id,
            'type' => (string) TimeOffType::Vacation->value,
            'start_date' => '2025-01-15T00:00:00.000Z',
            'end_date' => '2025-01-17T00:00:00.000Z',
            'is_half_day' => false,
        ]);

    $response->assertRedirect(route('employee.overview'))
        ->assertSessionHas('status', 'Time off request submitted successfully.');

    $this->assertDatabaseHas('time_off_requests', [
        'employee_id' => $employee->id,
        'organization_id' => $organization->id,
        'type' => TimeOffType::Vacation->value,
    ]);
});

test('validates required fields when storing time off request', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    /** @var User $user */
    $user = User::factory()
        ->createQuietly();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->post(route('employee.time-off.store'), []);

    $response->assertSessionHasErrors([
        'employee_id',
        'organization_id',
        'type',
        'start_date',
        'end_date',
    ]);
});

test('validates end date must be after or equal to start date', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    /** @var User $user */
    $user = User::factory()
        ->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->post(route('employee.time-off.store'), [
            'employee_id' => $employee->id,
            'organization_id' => $organization->id,
            'type' => (string) TimeOffType::Vacation->value,
            'start_date' => '2025-01-17',
            'end_date' => '2025-01-15',
            'is_half_day' => false,
        ]);

    $response->assertSessionHasErrors(['start_date', 'end_date']);
});
