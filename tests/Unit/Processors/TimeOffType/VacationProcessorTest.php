<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\VacationBalance;
use App\Processors\TimeOffType\VacationProcessor;

covers(VacationProcessor::class);

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Employee $employee */
        $employee = Employee::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        /** @var VacationProcessor $processor */
        $processor = app(VacationProcessor::class);

        $this->organization = $organization;
        $this->employee = $employee;
        $this->processor = $processor;
    });

test('deducts days from vacation balance when processed',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var VacationBalance $balance */
        $balance = VacationBalance::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'year' => now()->year,
            'from_last_year' => 0,
            'accrued' => 2000, // 20 days
            'taken' => 500, // 5 days
        ]);

        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3),
            'is_half_day' => false,
        ]);

        $this->processor->process($request);

        $balance->refresh();

        expect($balance->taken)->toBe(800); // 5 + 3 days = 8 days = 800
    });

test('restores days to vacation balance when reversed',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var VacationBalance $balance */
        $balance = VacationBalance::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'year' => now()->year,
            'from_last_year' => 0,
            'accrued' => 2000, // 20 days
            'taken' => 800, // 8 days
        ]);

        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3),
            'is_half_day' => false,
        ]);

        $this->processor->reverse($request);

        $balance->refresh();

        expect($balance->taken)->toBe(500); // 8 - 3 days = 5 days = 500
    });

test('deducts half day as 0.5',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var VacationBalance $balance */
        $balance = VacationBalance::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'year' => now()->year,
            'from_last_year' => 0,
            'accrued' => 2000, // 20 days
            'taken' => 500, // 5 days
        ]);

        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => now()->addDay(),
            'end_date' => null,
            'is_half_day' => true,
        ]);

        $this->processor->process($request);

        $balance->refresh();

        expect($balance->taken)
            ->toBe(550); // 5 + 0.5 days = 5.5 days = 550
    });
