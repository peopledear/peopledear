<?php

declare(strict_types=1);

use App\Enums\PeopleDear\EmploymentStatus;

test('has all expected cases', function (): void {
    $cases = EmploymentStatus::cases();

    expect($cases)
        ->toHaveCount(4)
        ->and(array_map(fn (EmploymentStatus $case) => $case->name, $cases))
        ->toBe([
            'Active',
            'Inactive',
            'OnLeave',
            'Terminated',
        ]);
});

test('has correct values', function (): void {
    expect(EmploymentStatus::Active->value)->toBe(1)
        ->and(EmploymentStatus::Inactive->value)->toBe(2)
        ->and(EmploymentStatus::OnLeave->value)->toBe(3)
        ->and(EmploymentStatus::Terminated->value)->toBe(4);
});

test('has correct labels', function (): void {
    expect(EmploymentStatus::Active->label())->toBe('Active')
        ->and(EmploymentStatus::Inactive->label())->toBe('Inactive')
        ->and(EmploymentStatus::OnLeave->label())->toBe('On Leave')
        ->and(EmploymentStatus::Terminated->label())->toBe('Terminated');
});

test('options method returns correct array', function (): void {
    $options = EmploymentStatus::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(4)
        ->and($options[1])->toBe('Active')
        ->and($options[2])->toBe('Inactive')
        ->and($options[3])->toBe('On Leave')
        ->and($options[4])->toBe('Terminated');
});
