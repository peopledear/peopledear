<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Validators\TimeOffType\SickLeaveValidator;

covers(SickLeaveValidator::class);

beforeEach(function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create();

    /** @var Employee $employee */
    $employee = Employee::factory()->create([
        'organization_id' => $organization->id,
    ]);

    /** @var SickLeaveValidator $validator */
    $validator = app(SickLeaveValidator::class);

    $this->organization = $organization;
    $this->employee = $employee;
    $this->validator = $validator;
});

test('validates successfully for future date', function (): void {
    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::SickLeave->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDays(3)->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('validates successfully for today', function (): void {
    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::SickLeave->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('fails validation when end date is before start date', function (): void {
    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::SickLeave->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDays(3)->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeFalse()
        ->and($result->errors)->toHaveKey('end_date');
});

test('validates successfully with null end date', function (): void {
    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::SickLeave->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'is_half_day' => true,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});

test('does not check balance for sick leave', function (): void {
    $data = [
        'organization_id' => $this->organization->id,
        'employee_id' => $this->employee->id,
        'type' => TimeOffType::SickLeave->value,
        'start_date' => Illuminate\Support\Facades\Date::now()->addDay()->toDateString(),
        'end_date' => Illuminate\Support\Facades\Date::now()->addDays(100)->toDateString(),
        'is_half_day' => false,
    ];

    $result = $this->validator->validate($data);

    expect($result->valid)->toBeTrue()
        ->and($result->errors)->toBeEmpty();
});
