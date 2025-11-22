<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;

test('has all expected cases', function (): void {
    $cases = RequestStatus::cases();

    expect($cases)
        ->toHaveCount(4)
        ->and(array_map(fn (RequestStatus $case) => $case->name, $cases))
        ->toBe([
            'Pending',
            'Approved',
            'Rejected',
            'Cancelled',
        ]);
});

test('has correct values', function (): void {
    expect(RequestStatus::Pending->value)->toBe(1)
        ->and(RequestStatus::Approved->value)->toBe(2)
        ->and(RequestStatus::Rejected->value)->toBe(3)
        ->and(RequestStatus::Cancelled->value)->toBe(4);
});

test('has correct labels', function (): void {
    expect(RequestStatus::Pending->label())->toBe('Pending')
        ->and(RequestStatus::Approved->label())->toBe('Approved')
        ->and(RequestStatus::Rejected->label())->toBe('Rejected')
        ->and(RequestStatus::Cancelled->label())->toBe('Cancelled');
});

test('options method returns correct array', function (): void {
    $options = RequestStatus::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(4)
        ->and($options[1])->toBe('Pending')
        ->and($options[2])->toBe('Approved')
        ->and($options[3])->toBe('Rejected')
        ->and($options[4])->toBe('Cancelled');
});
