<?php

declare(strict_types=1);

use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use Spatie\LaravelData\Optional;

test('creates data with all fields', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
        'vat_number' => 'VAT123',
        'ssn' => 'SSN123',
        'phone' => '+1234567890',
    ]);

    expect($data->name)
        ->toBe('Test Company')
        ->and($data->vat_number)
        ->toBe('VAT123')
        ->and($data->ssn)
        ->toBe('SSN123')
        ->and($data->phone)
        ->toBe('+1234567890');
});

test('creates data with partial fields using Optional', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
    ]);

    expect($data->name)
        ->toBe('Test Company')
        ->and($data->vat_number)
        ->toBeInstanceOf(Optional::class)
        ->and($data->ssn)
        ->toBeInstanceOf(Optional::class)
        ->and($data->phone)
        ->toBeInstanceOf(Optional::class);
});

test('toArray excludes optional fields', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
        'phone' => null,
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['name', 'phone'])
        ->not->toHaveKey('vat_number')
        ->not->toHaveKey('ssn')
        ->and($array['name'])
        ->toBe('Test Company')
        ->and($array['phone'])
        ->toBeNull();
});

test('can explicitly set fields to null', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    expect($data->name)
        ->toBe('Test Company')
        ->and($data->vat_number)
        ->toBeNull()
        ->and($data->ssn)
        ->toBeNull()
        ->and($data->phone)
        ->toBeNull();

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['name', 'vat_number', 'ssn', 'phone'])
        ->and($array['vat_number'])
        ->toBeNull()
        ->and($array['phone'])
        ->toBeNull();
});

test('empty array creates all optional fields', function (): void {
    $data = UpdateOrganizationData::from([]);

    expect($data->name)
        ->toBeInstanceOf(Optional::class)
        ->and($data->vat_number)
        ->toBeInstanceOf(Optional::class)
        ->and($data->ssn)
        ->toBeInstanceOf(Optional::class)
        ->and($data->phone)
        ->toBeInstanceOf(Optional::class);

    $array = $data->toArray();

    expect($array)->toBeEmpty();
});
