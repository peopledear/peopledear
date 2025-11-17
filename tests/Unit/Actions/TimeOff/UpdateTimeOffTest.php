<?php

declare(strict_types=1);

use App\Actions\TimeOff\UpdateTimeOffAction;
use App\Data\UpdateTimeOffData;
use App\Enums\PeopleDear\TimeOffStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\TimeOff;

beforeEach(function (): void {
    $this->action = app(UpdateTimeOffAction::class);
});

test('updates time off with all fields', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'type' => TimeOffType::Vacation,
        'status' => TimeOffStatus::Pending,
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = new UpdateTimeOffData(
        type: TimeOffType::SickLeave,
        status: TimeOffStatus::Approved,
        start_date: Illuminate\Support\Facades\Date::parse('2024-07-01'),
        end_date: Illuminate\Support\Facades\Date::parse('2024-07-10'),
        is_half_day: false,
    );

    $result = $this->action->handle($data, $timeOff);

    expect($result->type)->toBe(TimeOffType::SickLeave)
        ->and($result->status)->toBe(TimeOffStatus::Approved)
        ->and($result->start_date->format('Y-m-d'))->toBe('2024-07-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-07-10')
        ->and($result->is_half_day)->toBeFalse();
});

test('updates time off with partial fields', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'type' => TimeOffType::Vacation,
        'status' => TimeOffStatus::Pending,
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffData::from([
        'status' => TimeOffStatus::Approved,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(TimeOffStatus::Approved)
        ->and($result->type)->toBe(TimeOffType::Vacation)
        ->and($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05')
        ->and($result->is_half_day)->toBeFalse();
});

test('updates time off clearing end_date', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffData::from([
        'end_date' => null,
        'is_half_day' => true,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->end_date)->toBeNull()
        ->and($result->is_half_day)->toBeTrue();
});

test('updates time off status from pending to approved', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->pending()->createQuietly();

    $data = UpdateTimeOffData::from([
        'status' => TimeOffStatus::Approved,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(TimeOffStatus::Approved);
});

test('updates time off status from pending to rejected', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->pending()->createQuietly();

    $data = UpdateTimeOffData::from([
        'status' => TimeOffStatus::Rejected,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(TimeOffStatus::Rejected);
});

test('updates time off status from approved to cancelled', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->approved()->createQuietly();

    $data = UpdateTimeOffData::from([
        'status' => TimeOffStatus::Cancelled,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->status)->toBe(TimeOffStatus::Cancelled);
});

test('updates time off type', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'type' => TimeOffType::Vacation,
    ]);

    $data = UpdateTimeOffData::from([
        'type' => TimeOffType::PersonalDay,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->type)->toBe(TimeOffType::PersonalDay);
});

test('converts multi day to half day', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->multiDay()->createQuietly();

    $data = UpdateTimeOffData::from([
        'end_date' => null,
        'is_half_day' => true,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->is_half_day)->toBeTrue()
        ->and($result->end_date)->toBeNull();
});

test('converts half day to multi day', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->halfDay()->createQuietly([
        'start_date' => '2024-06-01',
    ]);

    $data = UpdateTimeOffData::from([
        'end_date' => Illuminate\Support\Facades\Date::parse('2024-06-05'),
        'is_half_day' => false,
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->is_half_day)->toBeFalse()
        ->and($result->end_date)->not->toBeNull()
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05');
});

test('updates only start date', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffData::from([
        'start_date' => Illuminate\Support\Facades\Date::parse('2024-07-01'),
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->start_date->format('Y-m-d'))->toBe('2024-07-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-05');
});

test('updates only end date', function (): void {
    /** @var TimeOff $timeOff */
    $timeOff = TimeOff::factory()->createQuietly([
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-05',
        'is_half_day' => false,
    ]);

    $data = UpdateTimeOffData::from([
        'end_date' => Illuminate\Support\Facades\Date::parse('2024-06-10'),
    ]);

    $result = $this->action->handle($data, $timeOff);

    expect($result->start_date->format('Y-m-d'))->toBe('2024-06-01')
        ->and($result->end_date->format('Y-m-d'))->toBe('2024-06-10');
});
