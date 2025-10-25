<?php

declare(strict_types=1);

use App\Models\Organization;

test('organization has correct fillable attributes', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Company',
        'vat_number' => '1234567890',
        'ssn' => '12-3456789',
        'phone' => '+1234567890',
    ]);

    expect($organization->name)
        ->toBe('Test Company')
        ->and($organization->vat_number)
        ->toBe('1234567890')
        ->and($organization->ssn)
        ->toBe('12-3456789')
        ->and($organization->phone)
        ->toBe('+1234567890');
});

test('organization model has correct casts', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $casts = $organization->getCasts();

    expect($casts)
        ->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('vat_number')
        ->toHaveKey('ssn')
        ->toHaveKey('phone');
});

test('organization can be created with only required fields', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Minimal Org',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    expect($organization->name)
        ->toBe('Minimal Org')
        ->and($organization->vat_number)
        ->toBeNull()
        ->and($organization->ssn)
        ->toBeNull()
        ->and($organization->phone)
        ->toBeNull();
});
