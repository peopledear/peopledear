<?php

declare(strict_types=1);

use App\Enums\EmploymentStatus;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('employee has organization relationship', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    expect($employee->organization())
        ->toBeInstanceOf(BelongsTo::class);
});

test('employee has office relationship', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()
        ->createQuietly();

    expect($employee->office())
        ->toBeInstanceOf(BelongsTo::class);
});

test('employee organization relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()
        ->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $employee->load('organization');

    expect($employee->organization)
        ->toBeInstanceOf(Organization::class)
        ->and($employee->organization->id)
        ->toBe($organization->id);
});

test('employee office relationship is properly loaded', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->createQuietly([
            'office_id' => $office->id,
        ]);

    $employee->load('office');

    expect($employee->office)
        ->toBeInstanceOf(Office::class)
        ->and($employee->office->id)
        ->toBe($office->id);
});

test('employee office can be null', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'office_id' => null,
    ]);

    $employee->load('office');

    expect($employee->office)->toBeNull();
});

test('employee has user relationship', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly();

    expect($employee->user())->toBeInstanceOf(BelongsTo::class);
});

test('employee user relationship is properly loaded', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'user_id' => $user->id,
    ]);

    $employee->load('user');

    expect($employee->user)
        ->toBeInstanceOf(User::class)
        ->and($employee->user->id)
        ->toBe($user->id);
});

test('employee user can be null', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'user_id' => null,
    ]);

    $employee->load('user');

    expect($employee->user)->toBeNull();
});

test('employee employment_status is cast to EmploymentStatus enum', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'employment_status' => EmploymentStatus::Active,
    ]);

    expect($employee->employment_status)
        ->toBeInstanceOf(EmploymentStatus::class)
        ->and($employee->employment_status)
        ->toBe(EmploymentStatus::Active)
        ->and($employee->employment_status->value)
        ->toBe(1)
        ->and($employee->employment_status->label())
        ->toBe('Active');
});

test('employee hire_date is cast to date', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'hire_date' => '2024-01-15',
    ]);

    expect($employee->hire_date)
        ->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($employee->hire_date->format('Y-m-d'))
        ->toBe('2024-01-15');
});

test('employee number is unique', function (): void {
    /** @var Employee $employee1 */
    $employee1 = Employee::factory()->createQuietly([
        'employee_number' => 'EMP-1234',
    ]);

    expect(fn () => Employee::factory()->createQuietly([
        'employee_number' => 'EMP-1234',
    ]))
        ->toThrow(Illuminate\Database\QueryException::class);
});

test('employee email can be null', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'email' => null,
    ]);

    expect($employee->email)->toBeNull();
});

test('employee job_title can be null', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'job_title' => null,
    ]);

    expect($employee->job_title)->toBeNull();
});

test('employee hire_date can be null', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()->createQuietly([
        'hire_date' => null,
    ]);

    expect($employee->hire_date)->toBeNull();
});

test('to array', function (): void {
    /** @var Employee $employee */
    $employee = Employee::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($employee->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'office_id',
            'user_id',
            'name',
            'email',
            'phone',
            'employee_number',
            'job_title',
            'hire_date',
            'employment_status',
        ]);
});
