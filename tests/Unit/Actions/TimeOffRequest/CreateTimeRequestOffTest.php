<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use Carbon\CarbonImmutable;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var CreateTimeOffRequest $action */
        $action = resolve(CreateTimeOffRequest::class);

        $this->action = $action;

        $this->organization = Organization::factory()
            ->create();

        $this->employee = Employee::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $this->period = Period::factory()
            ->for($this->organization)
            ->active()
            ->create();

        $this->vacationType = TimeOffType::factory()
            ->for($this->organization)
            ->requiresApproval()
            ->create([
                'name' => 'Vacation',
            ]);

        $this->sickLeaveType = TimeOffType::factory()
            ->for($this->organization)
            ->dontRequireApproval()
            ->create([
                'name' => 'Sick Leave',
            ]);

    });

test('creates time off with all fields',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            timeOffTypeId: $this->vacationType->id,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-05'),
            isHalfDay: false,
        );

        $timeOffRequest = $this->action->handle(
            $data,
            $this->employee,
            $this->vacationType,
        );

        expect($timeOffRequest)
            ->toBeInstanceOf(TimeOffRequest::class)
            ->and($timeOffRequest->organization_id)
            ->toBe($this->organization->id)
            ->and($timeOffRequest->employee_id)
            ->toBe($this->employee->id)
            ->and($timeOffRequest->type->id)
            ->toBe($this->vacationType->id)
            ->and($timeOffRequest->status)
            ->toBe(RequestStatus::Pending)
            ->and($timeOffRequest->start_date->format('Y-m-d'))
            ->toBe('2024-06-01')
            ->and($timeOffRequest->end_date->format('Y-m-d'))
            ->toBe('2024-06-05')
            ->and($timeOffRequest->is_half_day)->toBeFalse();
    });

test('creates half day time off with null end_date',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            timeOffTypeId: $this->vacationType->id,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: null,
            isHalfDay: true,
        );

        $timeOffRequest = $this->action->handle(
            $data,
            $this->employee,
            $this->vacationType,
        );

        expect($timeOffRequest)
            ->toBeInstanceOf(TimeOffRequest::class)
            ->and($timeOffRequest->type->id)
            ->toBe($this->vacationType->id)
            ->and($timeOffRequest->status)
            ->toBe(RequestStatus::Pending)
            ->and($timeOffRequest->start_date->format('Y-m-d'))
            ->toBe('2024-06-01')
            ->and($timeOffRequest->end_date)
            ->toBeNull()
            ->and($timeOffRequest->is_half_day)
            ->toBeTrue();
    });

test('creates multi day time off with end_date',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            timeOffTypeId: $this->vacationType->id,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-10'),
            isHalfDay: false,
        );

        $timeOffRequest = $this->action->handle(
            $data,
            $this->employee,
            $this->vacationType,
        );

        expect($timeOffRequest->is_half_day)
            ->toBeFalse()
            ->and($timeOffRequest->end_date)
            ->not->toBeNull()
            ->and($timeOffRequest->end_date->format('Y-m-d'))
            ->toBe('2024-06-10')
            ->and($timeOffRequest->start_date->format('Y-m-d'))
            ->toBe('2024-06-01');
    });

test('time off request with time off type that does not require approval is auto approved',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = CreateTimeOffRequestData::from([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'period_id' => $this->period->id,
            'timeOffTypeId' => $this->sickLeaveType->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1),
            'is_half_day' => false,
        ]);

        $timeOffRequest = $this->action->handle(
            $data,
            $this->employee,
            $this->sickLeaveType
        );

        expect($timeOffRequest)
            ->not->toBeNull()
            ->status
            ->toBe(RequestStatus::Approved);
    });

test('test time off request type with tupe that requires approval is pending approval',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = CreateTimeOffRequestData::from([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'period_id' => $this->period->id,
            'time_off_type_id' => $this->vacationType->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'is_half_day' => false,
        ]);

        $timeOffRequest = $this->action->handle(
            $data,
            $this->employee,
            $this->vacationType,
        );

        expect($timeOffRequest)
            ->not->toBeNull()
            ->status
            ->toBe(RequestStatus::Pending);
    });
