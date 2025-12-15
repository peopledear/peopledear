<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\UpdateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\UpdateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

beforeEach(function (): void {
    $this->action = resolve(UpdateTimeOffRequest::class);

    $this->vacationType = TimeOffType::factory()
        ->requiresApproval()
        ->create([
            'name' => 'Vacation',
        ]);

    $this->sickLeaveType = TimeOffType::factory()
        ->dontRequireApproval()
        ->create([
            'name' => 'Sick Leave',
        ]);

});

test('updates time off with all fields',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->create([
            'time_off_type_id' => $this->vacationType->id,
            'status' => RequestStatus::Pending,
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $attributes = new UpdateTimeOffRequestData(
            timeOffTypeId: $this->sickLeaveType->id,
            status: RequestStatus::Approved,
            startDate: CarbonImmutable::parse('2024-07-01'),
            endDate: CarbonImmutable::parse('2024-07-10'),
            isHalfDay: false,
        );

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)
            ->type->id
            ->toBe($this->sickLeaveType->id)
            ->and($timeOffRequest->status)
            ->toBe(RequestStatus::Approved)
            ->and($timeOffRequest->start_date->format('Y-m-d'))
            ->toBe('2024-07-01')
            ->and($timeOffRequest->end_date->format('Y-m-d'))
            ->toBe('2024-07-10')
            ->and($timeOffRequest->is_half_day)
            ->toBeFalse();
    });

test('updates time off with partial fields',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->create([
            'time_off_type_id' => $this->vacationType->id,
            'status' => RequestStatus::Pending,
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $attributes = UpdateTimeOffRequestData::from([
            'status' => RequestStatus::Approved,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest->status)
            ->toBe(RequestStatus::Approved)
            ->and($timeOffRequest)->type->id
            ->toBe($this->vacationType->id)
            ->and($timeOffRequest->start_date->format('Y-m-d'))
            ->toBe('2024-06-01')
            ->and($timeOffRequest->end_date->format('Y-m-d'))
            ->toBe('2024-06-05')
            ->and($timeOffRequest->is_half_day)
            ->toBeFalse();
    });

test('updates time off clearing end_date',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->create([
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $attributes = UpdateTimeOffRequestData::from([
            'end_date' => null,
            'is_half_day' => true,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest->end_date)
            ->toBeNull()
            ->and($timeOffRequest->is_half_day)
            ->toBeTrue();
    });

test('updates time off status from pending to approved',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->pending()->create();

        $attributes = UpdateTimeOffRequestData::from([
            'status' => RequestStatus::Approved,
        ]);

        $result = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($result->status)
            ->toBe(RequestStatus::Approved);
    });

test('updates time off status from pending to rejected',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()
            ->pending()
            ->create();

        $attributes = UpdateTimeOffRequestData::from([
            'status' => RequestStatus::Rejected,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest->status)
            ->toBe(RequestStatus::Rejected);
    });

test('updates time off status from approved to cancelled',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()
            ->approved()
            ->create();

        $attributes = UpdateTimeOffRequestData::from([
            'status' => RequestStatus::Cancelled,
        ]);

        $result = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($result)
            ->status
            ->toBe(RequestStatus::Cancelled);
    });

test('updates time off type',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->create([
            'time_off_type_id' => $this->vacationType->id,
        ]);

        $attributes = UpdateTimeOffRequestData::from([
            'time_off_type_id' => $this->sickLeaveType->id,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)->type->id
            ->toBe($this->sickLeaveType->id);
    });

test('converts multi day to half day',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()
            ->multiDay()
            ->create();

        $attributes = UpdateTimeOffRequestData::from([
            'end_date' => null,
            'is_half_day' => true,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)->is_half_day
            ->toBeTrue()
            ->and($timeOffRequest)->end_date
            ->toBeNull();
    });

test('converts half day to multi day',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()
            ->halfDay()
            ->create([
                'start_date' => '2024-06-01',
            ]);

        $attributes = UpdateTimeOffRequestData::from([
            'end_date' => Date::parse('2024-06-05'),
            'is_half_day' => false,
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)->is_half_day
            ->toBeFalse()
            ->and($timeOffRequest)->end_date
            ->not->toBeNull()
            ->and($timeOffRequest)->end_date->format('Y-m-d')
            ->toBe('2024-06-05');
    });

test('updates only start date',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()
            ->create([
                'start_date' => '2024-06-01',
                'end_date' => '2024-06-05',
                'is_half_day' => false,
            ]);

        $attributes = UpdateTimeOffRequestData::from([
            'start_date' => Date::parse('2024-07-01'),
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)->start_date->format('Y-m-d')
            ->toBe('2024-07-01')
            ->and($timeOffRequest)->end_date->format('Y-m-d')
            ->toBe('2024-06-05');
    });

test('updates only end date',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOffRequest */
        $timeOffRequest = TimeOffRequest::factory()->create([
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $attributes = UpdateTimeOffRequestData::from([
            'end_date' => Date::parse('2024-06-10'),
        ]);

        $timeOffRequest = $this->action->handle(
            $timeOffRequest,
            $attributes
        );

        expect($timeOffRequest)->start_date->format('Y-m-d')
            ->toBe('2024-06-01')
            ->and($timeOffRequest)->end_date->format('Y-m-d')
            ->toBe('2024-06-10');
    });
