<?php

declare(strict_types=1);

use App\Actions\TimeOffRequest\UpdateTimeOffRequest;
use App\Data\PeopleDear\TimeOffRequest\UpdateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\TimeOffRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

beforeEach(function (): void {
    $this->action = app(UpdateTimeOffRequest::class);
});

test('updates time off with all fields',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOff */
        $timeOff = TimeOffRequest::factory()->create([
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $data = new UpdateTimeOffRequestData(
            type: TimeOffType::SickLeave,
            status: RequestStatus::Approved,
            startDate: CarbonImmutable::parse('2024-07-01'),
            endDate: CarbonImmutable::parse('2024-07-10'),
            isHalfDay: false,
        );

        $result = $this->action->handle($data, $timeOff);

        expect($result->type)->toBe(TimeOffType::SickLeave)
            ->and($result->status)->toBe(RequestStatus::Approved)
            ->and($result->start_date->format('Y-m-d'))->toBe('2024-07-01')
            ->and($result->end_date->format('Y-m-d'))->toBe('2024-07-10')
            ->and($result->is_half_day)->toBeFalse();
    });

test('updates time off with partial fields',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var TimeOffRequest $timeOff */
        $timeOff = TimeOffRequest::factory()->create([
            'type' => TimeOffType::Vacation,
            'status' => RequestStatus::Pending,
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
            'is_half_day' => false,
        ]);

        $data = UpdateTimeOffRequestData::from([
            'status' => RequestStatus::Approved,
        ]);

        $result = $this->action->handle($data, $timeOff);

        expect($result->status)->toBe(RequestStatus::Approved)
            ->and($result->type)->toBe(TimeOffType::Vacation)
            ->and($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
            ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05')
            ->and($result->is_half_day)->toBeFalse();
    });

test('updates time off clearing end_date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffRequestData::from([
        'end_date' => null,
        'is_half_day' => true,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->end_date)->toBeNull()
        ->and($result->is_half_day)->toBeTrue();
});

test('updates time off status from pending to approved', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->pending()->create();

    $data = UpdateTimeOffRequestData::from([
        'status' => RequestStatus::Approved,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(RequestStatus::Approved);
});

test('updates time off status from pending to rejected', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->pending()->create();

    $data = UpdateTimeOffRequestData::from([
        'status' => RequestStatus::Rejected,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(RequestStatus::Rejected);
});

test('updates time off status from approved to cancelled', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->approved()->create();

    $data = UpdateTimeOffRequestData::from([
        'status' => RequestStatus::Cancelled,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(RequestStatus::Cancelled);
});

test('updates time off type', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'type' => TimeOffType::Vacation,
    ]);

    $data = UpdateTimeOffRequestData::from([
        'type' => TimeOffType::PersonalDay,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->type)->toBe(TimeOffType::PersonalDay);
});

test('converts multi day to half day', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->multiDay()->create();

    $data = UpdateTimeOffRequestData::from([
        'end_date' => null,
        'is_half_day' => true,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->is_half_day)->toBeTrue()
        ->and($result->end_date)->toBeNull();
});

test('converts half day to multi day', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->halfDay()->create([
        'start_date' => '2024-06-01',
    ]);

    $data = UpdateTimeOffRequestData::from([
        'end_date' => Date::parse('2024-06-05'),
        'is_half_day' => false,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->is_half_day)->toBeFalse()
        ->and($result->end_date)->not->toBeNull()
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05');
});

test('updates only start date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffRequestData::from([
        'start_date' => Date::parse('2024-07-01'),
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->start_date->format('Y-m-d'))->toBe('2024-07-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05');
});

test('updates only end date', function (): void {
    /** @var TimeOffRequest $timeOff */
    $timeOff = TimeOffRequest::factory()->create([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffRequestData::from([
        'end_date' => Date::parse('2024-06-10'),
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-10');
});
