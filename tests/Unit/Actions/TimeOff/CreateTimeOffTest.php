<?php

declare(strict_types=1);

use App\Actions\TimeOff\CreateTimeOffAction;
use App\Data\CreateTimeOffData;
use App\Enums\PeopleDear\TimeOffStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOff;

beforeEach(function (): void {
    $this->action = app(CreateTimeOffAction::class);
});

test('creates time off with all fields', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::Vacation,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: Illuminate\Support\Facades\Date::parse('2024-06-05'),
        is_half_day: false,
    );

    $result = $this->action->handle($data);

    expect($result)
        ->toBeInstanceOf(TimeOff::class)
        ->and($result->organization_id)->toBe($organization->id)
        ->and($result->employee_id)->toBe($employee->id)
        ->and($result->type)->toBe(TimeOffType::Vacation)
        ->and($result->status)->toBe(TimeOffStatus::Pending)
        ->and($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05')
        ->and($result->is_half_day)->toBeFalse();
});

test('creates half day time off with null end_date', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::SickLeave,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: null,
        is_half_day: true,
    );

    $result = $this->action->handle($data);

    expect($result)
        ->toBeInstanceOf(TimeOff::class)
        ->and($result->type)->toBe(TimeOffType::SickLeave)
        ->and($result->status)->toBe(TimeOffStatus::Pending)
        ->and($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
        ->and($result->end_date)->toBeNull()
        ->and($result->is_half_day)->toBeTrue();
});

test('creates time off with personal day type', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::PersonalDay,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: null,
        is_half_day: true,
    );

    $result = $this->action->handle($data);

    expect($result->type)->toBe(TimeOffType::PersonalDay)
        ->and($result->status)->toBe(TimeOffStatus::Pending);
});

test('creates time off with bereavement type', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::Bereavement,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: Illuminate\Support\Facades\Date::parse('2024-06-03'),
        is_half_day: false,
    );

    $result = $this->action->handle($data);

    expect($result->type)->toBe(TimeOffType::Bereavement)
        ->and($result->status)->toBe(TimeOffStatus::Pending)
        ->and($result->end_date)->not->toBeNull();
});

test('always sets status to pending on creation', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::Vacation,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: Illuminate\Support\Facades\Date::parse('2024-06-05'),
        is_half_day: false,
    );

    $result = $this->action->handle($data);

    expect($result->status)->toBe(TimeOffStatus::Pending);
});

test('creates multi day time off with end_date', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $data = new CreateTimeOffData(
        organization_id: $organization->id,
        employee_id: $employee->id,
        type: TimeOffType::Vacation,
        start_date: Illuminate\Support\Facades\Date::parse('2024-06-01'),
        end_date: Illuminate\Support\Facades\Date::parse('2024-06-10'),
        is_half_day: false,
    );

    $result = $this->action->handle($data);

    expect($result->is_half_day)->toBeFalse()
        ->and($result->end_date)->not->toBeNull()
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-10')
        ->and($result->start_date->format('Y-m-d'))->toBe('2024-06-01');
});
