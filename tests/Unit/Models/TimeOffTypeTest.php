<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\PeopleDear\CarryOverType;
use App\Enums\TimeOffUnit;
use App\Models\TimeOffType;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

test('time off type can be created with balance config data', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create([
            'balance_config' => new TimeOffTypeBalanceConfigData(
                accrualDaysPerYear: 22,
                carryOverType: CarryOverType::Limited,
                carryOverDaysLimit: 5,
            ),
        ])
        ->fresh();

    expect($timeOffType)
        ->toBeInstanceOf(TimeOffType::class)
        ->and($timeOffType->balance_config)
        ->toBeInstanceOf(TimeOffTypeBalanceConfigData::class)
        ->and($timeOffType->balance_config->carryOverType)
        ->toBe(CarryOverType::Limited)
        ->and($timeOffType->balance_config->accrualDaysPerYear)
        ->toBe(22)
        ->and($timeOffType->balance_config->carryOverDaysLimit)
        ->toBe(5);

});

test('balance config casts to a balance config data object', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect($timeOffType->balance_config)
        ->toBeInstanceOf(TimeOffTypeBalanceConfigData::class);

});

test('fall back approval role relationship', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect($timeOffType->fallbackApprovalRole)
        ->toBeInstanceOf(Role::class)
        ->and($timeOffType->fallbackApprovalRole->id)
        ->toBe($timeOffType->fallback_approval_role_id);

});

test('allowed units is an array of time off unit enums', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create([
            'allowed_units' => [
                TimeOffUnit::Day,
                TimeOffUnit::Hour,
            ],
        ])
        ->fresh();

    expect($timeOffType->allowed_units)
        ->toBeArray()
        ->each->toBeInstanceOf(TimeOffUnit::class)
        ->and(Arr::first($timeOffType->allowed_units))
        ->toBe(TimeOffUnit::Day)
        ->and(Arr::last($timeOffType->allowed_units))
        ->toBe(TimeOffUnit::Hour);

});

test('to array', function (): void {

    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect(array_keys($timeOffType->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'organization_id',
            'fallback_approval_role_id',
            'name',
            'description',
            'is_system',
            'allowed_units',
            'icon',
            'color',
            'status',
            'requires_approval',
            'requires_justification',
            'requires_justification_document',
            'balance_mode',
            'balance_config',
        ]);

});

test('time off requests relationship', function (): void {
    $timeOffType = TimeOffType::factory()
        ->withFallbackApprovalRole()
        ->create()
        ->fresh();

    expect($timeOffType->timeOffRequests())
        ->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});
