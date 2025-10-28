<?php

declare(strict_types=1);

use App\Enums\PeopleDear\OfficeType;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

test('office has organization relationship', function (): void {
    /** @var Office $office */
    $office = Office::factory()->createQuietly();

    expect($office->organization())->toBeInstanceOf(BelongsTo::class);
});

test('office has address relationship', function (): void {
    /** @var Office $office */
    $office = Office::factory()->createQuietly();

    expect($office->address())->toBeInstanceOf(MorphOne::class);
});

test('office organization relationship is properly loaded', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var Office $office */
    $office = Office::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    $office->load('organization');

    expect($office->organization)
        ->toBeInstanceOf(Organization::class)
        ->and($office->organization->id)
        ->toBe($organization->id);
});

test('office address relationship is properly loaded', function (): void {
    /** @var Office $office */
    $office = Office::factory()->createQuietly();

    /** @var Address $address */
    $address = Address::factory()
        ->for($office, 'addressable')
        ->createQuietly();

    $office->load('address');

    expect($office->address)
        ->toBeInstanceOf(Address::class)
        ->and($office->address->id)
        ->toBe($address->id)
        ->and($office->address->addressable_id)
        ->toBe($office->id)
        ->and($office->address->addressable_type)
        ->toBe(Office::class);
});

test('office type is cast to OfficeType enum', function (): void {
    /** @var Office $office */
    $office = Office::factory()->createQuietly([
        'type' => OfficeType::Headquarters,
    ]);

    expect($office->type)
        ->toBeInstanceOf(OfficeType::class)
        ->and($office->type)
        ->toBe(OfficeType::Headquarters)
        ->and($office->type->value)
        ->toBe(1)
        ->and($office->type->label())
        ->toBe('Headquarters');
});

test('to array', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($office->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'name',
            'type',
            'phone',
        ]);
});
