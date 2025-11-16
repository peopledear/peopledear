<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffType;

test('has all expected cases', function (): void {
    $cases = TimeOffType::cases();

    expect($cases)
        ->toHaveCount(4)
        ->and(array_map(fn (TimeOffType $case) => $case->name, $cases))
        ->toBe([
            'Vacation',
            'SickLeave',
            'PersonalDay',
            'Bereavement',
        ]);
});

test('has correct values', function (): void {
    expect(TimeOffType::Vacation->value)->toBe(1)
        ->and(TimeOffType::SickLeave->value)->toBe(2)
        ->and(TimeOffType::PersonalDay->value)->toBe(3)
        ->and(TimeOffType::Bereavement->value)->toBe(4);
});

test('has correct labels', function (): void {
    expect(TimeOffType::Vacation->label())->toBe('Vacation')
        ->and(TimeOffType::SickLeave->label())->toBe('Sick Leave')
        ->and(TimeOffType::PersonalDay->label())->toBe('Personal Day')
        ->and(TimeOffType::Bereavement->label())->toBe('Bereavement');
});

test('options method returns correct array', function (): void {
    $options = TimeOffType::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(4)
        ->and($options[1])->toBe('Vacation')
        ->and($options[2])->toBe('Sick Leave')
        ->and($options[3])->toBe('Personal Day')
        ->and($options[4])->toBe('Bereavement');
});
