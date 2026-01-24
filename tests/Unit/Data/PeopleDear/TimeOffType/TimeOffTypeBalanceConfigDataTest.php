<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\CarryOverType;
use App\Enums\RecurringPeriod;

test('handles snake case recurring period key', function (): void {

    $balanceConfig = TimeOffTypeBalanceConfigData::from([
        'recurring_period' => RecurringPeriod::Weekly->value,
        'limit_per_period' => 5,
    ]);

    expect($balanceConfig->recurringPeriod)
        ->toBeInstanceOf(RecurringPeriod::class)
        ->and($balanceConfig->recurringPeriod)
        ->toBe(RecurringPeriod::Weekly)
        ->and($balanceConfig->limitPerPeriod)
        ->toBe(5);

});

test('recurring period accepts a recurring period enum', function (): void {

    $balanceConfig = new TimeOffTypeBalanceConfigData(
        recurringPeriod: RecurringPeriod::Yearly,
    );

    expect($balanceConfig->recurringPeriod)
        ->toBeInstanceOf(RecurringPeriod::class)
        ->and($balanceConfig->recurringPeriod)
        ->toBe(RecurringPeriod::Yearly);

});

test('casts recurring period int to recurring period enum', function (): void {

    $balanceConfig = TimeOffTypeBalanceConfigData::from([
        'recurringPeriod' => RecurringPeriod::Monthly->value,
    ]);

    expect($balanceConfig->recurringPeriod)
        ->toBeInstanceOf(RecurringPeriod::class)
        ->and($balanceConfig->recurringPeriod)
        ->toBe(RecurringPeriod::Monthly);

});

test('handles snake case keys', function (): void {

    $balanceConfig = TimeOffTypeBalanceConfigData::from([
        'accrual_days_per_year' => 15,
        'carry_over_type' => CarryOverType::None->value,
        'carry_over_days_limit' => 10,
        'carry_over_expiry_months' => 6,
    ]);

    expect($balanceConfig->accrualDaysPerYear)
        ->toBe(15)
        ->and($balanceConfig->carryOverType)
        ->toBeInstanceOf(CarryOverType::class)
        ->and($balanceConfig->carryOverType)
        ->toBe(CarryOverType::None)
        ->and($balanceConfig->carryOverDaysLimit)
        ->toBe(10)
        ->and($balanceConfig->carryOverExpiryMonths)
        ->toBe(6);

});

test('casts carry over type int to carry over enum', function (): void {

    $balanceConfig = TimeOffTypeBalanceConfigData::from([
        'carryOverType' => CarryOverType::Unlimited->value,
    ]);

    expect($balanceConfig->carryOverType)
        ->toBeInstanceOf(CarryOverType::class)
        ->and($balanceConfig->carryOverType)
        ->toBe(CarryOverType::Unlimited);

});

test('carry over type accepts a carry over enum', function (): void {

    $balanceConfig = new TimeOffTypeBalanceConfigData(
        carryOverType: CarryOverType::Limited,
    );

    expect($balanceConfig->carryOverType)
        ->toBeInstanceOf(CarryOverType::class)
        ->and($balanceConfig->carryOverType)
        ->toBe(CarryOverType::Limited);

});
