<?php

declare(strict_types=1);

use App\Enums\PeopleDear\TimeOffStatus;

test('has all expected cases', function (): void {
    $cases = TimeOffStatus::cases();

    expect($cases)
        ->toHaveCount(4)
        ->and(array_map(fn (TimeOffStatus $case) => $case->name, $cases))
        ->toBe([
            'Pending',
            'Approved',
            'Rejected',
            'Cancelled',
        ]);
});

test('has correct values', function (): void {
    expect(TimeOffStatus::Pending->value)->toBe(1)
        ->and(TimeOffStatus::Approved->value)->toBe(2)
        ->and(TimeOffStatus::Rejected->value)->toBe(3)
        ->and(TimeOffStatus::Cancelled->value)->toBe(4);
});

test('has correct labels', function (): void {
    expect(TimeOffStatus::Pending->label())->toBe('Pending')
        ->and(TimeOffStatus::Approved->label())->toBe('Approved')
        ->and(TimeOffStatus::Rejected->label())->toBe('Rejected')
        ->and(TimeOffStatus::Cancelled->label())->toBe('Cancelled');
});

test('options method returns correct array', function (): void {
    $options = TimeOffStatus::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(4)
        ->and($options[1])->toBe('Pending')
        ->and($options[2])->toBe('Approved')
        ->and($options[3])->toBe('Rejected')
        ->and($options[4])->toBe('Cancelled');
});
