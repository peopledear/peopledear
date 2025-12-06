<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\VacationBalance;
use App\Validators\TimeOffType\VacationValidator;

covers(VacationValidator::class);

beforeEach(function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    /** @var Employee $employee */
    $employee = Employee::factory()->create([
        'organization_id' => $organization->id,
    ]);

    /** @var VacationValidator $validator */
    $validator = app(VacationValidator::class);

    $this->organization = $organization;
    $this->employee = $employee;
    $this->validator = $validator;
});

test('validates successfully with sufficient balance', function (): void {
    VacationBalance::factory()->create([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => now()->year,
        'from_last_year' => 0,
        'accrued' => 2000, // 20 days
        'taken' => 500, // 5 days
    ]);

    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::Vacation->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDays(3)->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('fails validation with insufficient balance', function (): void {
    VacationBalance::factory()->create([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => now()->year,
        'from_last_year' => 0,
        'accrued' => 1000, // 10 days
        'taken' => 800, // 8 days (only 2 days available)
    ]);

    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::Vacation->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDays(5)->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toHaveKey('balance');
});

test('fails validation when end date is before start date', function (): void {
    VacationBalance::factory()->create([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => now()->year,
        'from_last_year' => 0,
        'accrued' => 2000, // 20 days
        'taken' => 0,
    ]);

    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::Vacation->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDays(3)->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toHaveKey('end_date');
});

test('calculates half day as 0.5 days', function (): void {
    VacationBalance::factory()->create([
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'year' => now()->year,
        'from_last_year' => 0,
        'accrued' => 100, // 1 day
        'taken' => 0,
    ]);

    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::Vacation->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'is_half_day' => true,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('fails validation when no balance exists', function (): void {

    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::Vacation->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toHaveKey('balance');
});
