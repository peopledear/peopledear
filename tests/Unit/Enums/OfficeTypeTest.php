<?php

declare(strict_types=1);

use App\Enums\OfficeType;

test('has all expected cases', function (): void {
    $cases = OfficeType::cases();

    expect($cases)
        ->toHaveCount(8)
        ->and(array_map(fn (OfficeType $case) => $case->name, $cases))
        ->toBe([
            'Headquarters',
            'Branch',
            'Warehouse',
            'Store',
            'Factory',
            'Remote',
            'Coworking',
            'HomeOffice',
        ]);
});

test('has correct values', function (): void {
    expect(OfficeType::Headquarters->value)->toBe(1)
        ->and(OfficeType::Branch->value)->toBe(2)
        ->and(OfficeType::Warehouse->value)->toBe(3)
        ->and(OfficeType::Store->value)->toBe(4)
        ->and(OfficeType::Factory->value)->toBe(5)
        ->and(OfficeType::Remote->value)->toBe(6)
        ->and(OfficeType::Coworking->value)->toBe(7)
        ->and(OfficeType::HomeOffice->value)->toBe(8);
});

test('has correct labels', function (): void {
    expect(OfficeType::Headquarters->label())->toBe('Headquarters')
        ->and(OfficeType::Branch->label())->toBe('Branch')
        ->and(OfficeType::Warehouse->label())->toBe('Warehouse')
        ->and(OfficeType::Store->label())->toBe('Store')
        ->and(OfficeType::Factory->label())->toBe('Factory')
        ->and(OfficeType::Remote->label())->toBe('Remote')
        ->and(OfficeType::Coworking->label())->toBe('Coworking')
        ->and(OfficeType::HomeOffice->label())->toBe('Home Office');
});

test('has correct icons', function (): void {
    expect(OfficeType::Headquarters->icon())->toBe('building-2')
        ->and(OfficeType::Branch->icon())->toBe('building')
        ->and(OfficeType::Warehouse->icon())->toBe('warehouse')
        ->and(OfficeType::Store->icon())->toBe('store')
        ->and(OfficeType::Factory->icon())->toBe('factory')
        ->and(OfficeType::Remote->icon())->toBe('globe')
        ->and(OfficeType::Coworking->icon())->toBe('users')
        ->and(OfficeType::HomeOffice->icon())->toBe('home');
});

test('options method returns correct array', function (): void {
    $options = OfficeType::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(8)
        ->and($options[1])->toBe('Headquarters')
        ->and($options[2])->toBe('Branch')
        ->and($options[8])->toBe('Home Office');
});
