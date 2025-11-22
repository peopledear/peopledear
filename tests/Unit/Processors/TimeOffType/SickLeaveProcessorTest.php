<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Processors\TimeOffType\SickLeaveProcessor;

covers(SickLeaveProcessor::class);

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

        /** @var SickLeaveProcessor $processor */
        $processor = app(SickLeaveProcessor::class);

        $this->organization = $organization;
        $this->employee = $employee;
        $this->processor = $processor;
    });

test('processes sick leave request without balance deduction',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::SickLeave,
            'status' => RequestStatus::Pending,
        ]);

        $this->processor->process($request);

        expect($request->refresh()->status)->toBe(RequestStatus::Approved);
    });

test('reverses sick leave processing',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::SickLeave,
            'status' => RequestStatus::Approved,
        ]);

        $this->processor->reverse($request);

        expect($request->refresh()->status)->toBe(RequestStatus::Cancelled);
    });

test('is idempotent for multiple process calls',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->createQuietly([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::SickLeave,
            'status' => RequestStatus::Pending,
        ]);

        $this->processor->process($request);
        $this->processor->process($request);

        expect($request->refresh()->status)->toBe(RequestStatus::Approved);
    });
