<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('time off model has period relationship', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly();

    expect($timeOff->period())
        ->toBeInstanceOf(BelongsTo::class);
});

test('time off has organization relationship', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly();

    expect($timeOff->organization())
        ->toBeInstanceOf(BelongsTo::class);
});

test('time off organization relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $timeOff->load('organization');

    expect($timeOff->organization)
        ->toBeInstanceOf(Organization::class)
        ->and($timeOff->organization->id)
        ->toBe($organization->id);
});

test('time off has employee relationship', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly();

    expect($timeOff->employee())
        ->toBeInstanceOf(BelongsTo::class);
});

test('time off employee relationship is properly loaded', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'employee_id' => $employee->id,
    ]);

    $timeOff->load('employee');

    expect($timeOff->employee)
        ->toBeInstanceOf(Employee::class)
        ->and($timeOff->employee->id)
        ->toBe($employee->id);
});

test('time off type is cast to TimeOffType enum', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'type' => TimeOffType::Vacation,
    ]);

    expect($timeOff->type)
        ->toBeInstanceOf(TimeOffType::class)
        ->and($timeOff->type)
        ->toBe(TimeOffType::Vacation)
        ->and($timeOff->type->value)
        ->toBe(1)
        ->and($timeOff->type->label())
        ->toBe('Vacation');
});

test('time off status is cast to TimeOffStatus enum', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'status' => RequestStatus::Pending,
    ]);

    expect($timeOff->status)
        ->toBeInstanceOf(RequestStatus::class)
        ->and($timeOff->status)
        ->toBe(RequestStatus::Pending)
        ->and($timeOff->status->value)
        ->toBe(1)
        ->and($timeOff->status->label())
        ->toBe('Pending');
});

test('time off start_date is cast to date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'start_date' => '2024-03-15',
    ]);

    expect($timeOff->start_date)
        ->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($timeOff->start_date->format('Y-m-d'))
        ->toBe('2024-03-15');
});

test('time off end_date is cast to date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'start_date' => '2024-03-15',
        'end_date' => '2024-03-20',
        'is_half_day' => false,
    ]);

    expect($timeOff->end_date)
        ->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($timeOff->end_date->format('Y-m-d'))
        ->toBe('2024-03-20');
});

test('time off end_date can be null', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'end_date' => null,
        'is_half_day' => true,
    ]);

    expect($timeOff->end_date)->toBeNull();
});

test('time off is_half_day is cast to boolean', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->createQuietly([
        'is_half_day' => true,
    ]);

    expect($timeOff->is_half_day)
        ->toBeBool()
        ->and($timeOff->is_half_day)
        ->toBeTrue();
});

test('time off half day has null end_date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->halfDay()->createQuietly();

    expect($timeOff->is_half_day)
        ->toBeTrue()
        ->and($timeOff->end_date)
        ->toBeNull();
});

test('time off multi day has end_date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->multiDay()->createQuietly();

    expect($timeOff->is_half_day)
        ->toBeFalse()
        ->and($timeOff->end_date)
        ->not->toBeNull()
        ->and($timeOff->end_date)
        ->toBeInstanceOf(Carbon\CarbonInterface::class);
});

test('time off pending state sets status correctly', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->pending()->createQuietly();

    expect($timeOff->status)
        ->toBe(RequestStatus::Pending);
});

test('time off approved state sets status correctly', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->approved()->createQuietly();

    expect($timeOff->status)
        ->toBe(RequestStatus::Approved);
});

test('time off rejected state sets status correctly', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->rejected()->createQuietly();

    expect($timeOff->status)
        ->toBe(RequestStatus::Rejected);
});

test('time off cancelled state sets status correctly', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->cancelled()->createQuietly();

    expect($timeOff->status)
        ->toBe(RequestStatus::Cancelled);
});

test('to array', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($timeOff->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'period_id',
            'employee_id',
            'type',
            'status',
            'start_date',
            'end_date',
            'is_half_day',
        ]);
});
