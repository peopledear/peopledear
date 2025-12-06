<?php

declare(strict_types=1);

use App\Models\Address;
use App\Models\Office;
use Illuminate\Database\Eloquent\Relations\MorphTo;

test('address has addressable relationship', function (): void {
    /** @var Address $address */
    $address = Address::factory()
        ->for(Office::factory(), 'addressable')
        ->create();

    expect($address->addressable())
        ->toBeInstanceOf(MorphTo::class);
});

test('address addressable relationship is properly loaded for office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->create();

    /** @var Address $address */
    $address = Address::factory()
        ->for($office, 'addressable')
        ->create();

    $address->load('addressable');

    expect($address->addressable)
        ->toBeInstanceOf(Office::class)
        ->and($address->addressable->id)
        ->toBe($office->id);
});

test('to array', function (): void {
    /** @var Address $address */
    $address = Address::factory()
        ->for(Office::factory(), 'addressable')
        ->create()
        ->refresh();

    expect(array_keys($address->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'addressable_type',
            'addressable_id',
            'line1',
            'line2',
            'city',
            'state',
            'postal_code',
            'country',
        ]);
});
