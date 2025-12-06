<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\CreateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Period;
use App\Models\TimeOffRequest;
use Carbon\CarbonImmutable;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var CreateTimeOffRequest $action */
        $action = app(CreateTimeOffRequest::class);

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
            type: TimeOffType::Vacation,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-05'),
            isHalfDay: false,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result)
            ->toBeInstanceOf(TimeOffRequest::class)
            ->and($result->organization_id)
            ->toBe($this->organization->id)
            ->and($result->employee_id)
            ->toBe($this->employee->id)
            ->and($result->type)
            ->toBe(TimeOffType::Vacation)
            ->and($result->status)
            ->toBe(RequestStatus::Pending)
            ->and($result->start_date->format('Y-m-d'))
            ->toBe('2024-06-01')
            ->and($result->end_date->format('Y-m-d'))
            ->toBe('2024-06-05')
            ->and($result->is_half_day)->toBeFalse();
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
            type: TimeOffType::SickLeave,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: null,
            isHalfDay: true,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result)
            ->toBeInstanceOf(TimeOffRequest::class)
            ->and($result->type)
            ->toBe(TimeOffType::SickLeave)
            ->and($result->status)
            ->toBe(RequestStatus::Approved) // Auto-approved
            ->and($result->start_date->format('Y-m-d'))
            ->toBe('2024-06-01')
            ->and($result->end_date)
            ->toBeNull()
            ->and($result->is_half_day)
            ->toBeTrue();
    });

test('creates time off with personal day type',
    /**
     * @throws Throwable
     */
    function (): void {
        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            type: TimeOffType::PersonalDay,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: null,
            isHalfDay: true,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result->type)
            ->toBe(TimeOffType::PersonalDay)
            ->and($result->status)
            ->toBe(RequestStatus::Pending);
    });

test('creates time off with bereavement type',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            type: TimeOffType::Bereavement,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-03'),
            isHalfDay: false,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result->type)
            ->toBe(TimeOffType::Bereavement)
            ->and($result->status)
            ->toBe(RequestStatus::Approved) // Auto-approved
            ->and($result->end_date)->not
            ->toBeNull();
    });

test('always sets status to pending on creation',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organizationId: $this->organization->id,
            employeeId: $this->employee->id,
            periodId: $this->period->id,
            type: TimeOffType::Vacation,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-05'),
            isHalfDay: false,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result->status)
            ->toBe(RequestStatus::Pending);
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
            type: TimeOffType::Vacation,
            startDate: CarbonImmutable::parse('2024-06-01'),
            endDate: CarbonImmutable::parse('2024-06-10'),
            isHalfDay: false,
        );

        $result = $this->action->handle($data, $this->employee);

        expect($result->is_half_day)
            ->toBeFalse()
            ->and($result->end_date)
            ->not->toBeNull()
            ->and($result->end_date->format('Y-m-d'))
            ->toBe('2024-06-10')
            ->and($result->start_date->format('Y-m-d'))
            ->toBe('2024-06-01');
    });
