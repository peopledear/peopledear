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
            ->createQuietly();

        $this->employee = Employee::factory()->createQuietly([
            'organization_id' => $this->organization->id,
        ]);

        $this->period = Period::factory()
            ->for($this->organization)
            ->active()
            ->createQuietly();

    });

test('creates time off with all fields',
    /**
     * @throws Throwable
     */
    function (): void {

        $data = new CreateTimeOffRequestData(
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::Vacation,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: CarbonImmutable::parse('2024-06-05'),
            is_half_day: false,
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
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::SickLeave,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: null,
            is_half_day: true,
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
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::PersonalDay,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: null,
            is_half_day: true,
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
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::Bereavement,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: CarbonImmutable::parse('2024-06-03'),
            is_half_day: false,
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
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::Vacation,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: CarbonImmutable::parse('2024-06-05'),
            is_half_day: false,
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
            organization_id: $this->organization->id,
            employee_id: $this->employee->id,
            period_id: $this->period->id,
            type: TimeOffType::Vacation,
            start_date: CarbonImmutable::parse('2024-06-01'),
            end_date: CarbonImmutable::parse('2024-06-10'),
            is_half_day: false,
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
