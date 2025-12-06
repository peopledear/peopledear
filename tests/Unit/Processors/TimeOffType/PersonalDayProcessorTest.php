<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Processors\TimeOffType\PersonalDayProcessor;

covers(PersonalDayProcessor::class);

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Employee $employee */
        $employee = Employee::factory()->create([
            'organization_id' => $organization->id,
        ]);

        /** @var PersonalDayProcessor $processor */
        $processor = app(PersonalDayProcessor::class);

        $this->organization = $organization;
        $this->employee = $employee;
        $this->processor = $processor;
    });

test('processes personal day request',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::PersonalDay,
            'status' => RequestStatus::Pending,
        ]);

        $this->processor->process($request);

        expect($request->refresh()->status)
            ->toBe(RequestStatus::Approved);
    });

test('reverses personal day processing',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $request */
        $request = TimeOffRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'employee_id' => $this->employee->id,
            'type' => TimeOffType::PersonalDay,
            'status' => RequestStatus::Approved,
        ]);

        $this->processor->reverse($request);

        expect($request->refresh()->status)
            ->toBe(RequestStatus::Cancelled);
    });
