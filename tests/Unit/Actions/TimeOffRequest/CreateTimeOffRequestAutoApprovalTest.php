<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;

beforeEach(function (): void {

    $this->organization = Organization::factory()
        ->create();

    $this->period = Period::factory()
        ->active()
        ->for($this->organization)
        ->create();

    $this->employee = Employee::factory()
        ->for($this->organization)
        ->create();

});

test('sick leave is auto-approved',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = CreateTimeOffRequestData::from([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'period_id' => $this->period->id,
            'type' => TimeOffType::SickLeave,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'is_half_day' => false,
        ]);

        $action = app(CreateTimeOffRequest::class);
        $result = $action->handle($data, $this->employee);

        $result->load('approval');

        expect($result->approval)
            ->not->toBeNull()
            ->and($result->approval->status)
            ->toBe(RequestStatus::Approved)
            ->and($result->approval->approved_at)
            ->not->toBeNull();
    });

test('vacation requires approval',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = CreateTimeOffRequestData::from([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'period_id' => $this->period->id,
            'type' => TimeOffType::Vacation,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'is_half_day' => false,
        ]);

        $action = app(CreateTimeOffRequest::class);
        $result = $action->handle($data, $this->employee);

        $result->load('approval');

        expect($result->approval)
            ->not->toBeNull()
            ->and($result->approval->status)
            ->toBe(RequestStatus::Pending)
            ->and($result->approval->approved_at)
            ->toBeNull();
    });

test('personal day requires approval',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = CreateTimeOffRequestData::from([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'period_id' => $this->period->id,
            'type' => TimeOffType::PersonalDay,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'is_half_day' => false,
        ]);

        $action = app(CreateTimeOffRequest::class);
        $result = $action->handle($data, $this->employee);

        $result->load('approval');

        expect($result->approval)
            ->not->toBeNull()
            ->and($result->approval->status)
            ->toBe(RequestStatus::Pending)
            ->and($result->approval->approved_at)
            ->toBeNull();
    });
