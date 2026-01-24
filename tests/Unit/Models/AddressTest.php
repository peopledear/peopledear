<?php

declare(strict_types=1);

use App\Models\Address;
use App\Models\Location;
use Illuminate\Database\Eloquent\Relations\MorphTo;

test('address has addressable relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Address $address */
        $address = Address::factory()
            ->for(Location::factory(), 'addressable')
            ->createQuietly();

        expect($address->addressable())
            ->toBeInstanceOf(MorphTo::class);
    });

test('address addressable relationship is properly loaded for location',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->createQuietly();

        $address->load('addressable');

        expect($address->addressable)
            ->toBeInstanceOf(Location::class)
            ->and($address->addressable->id)
            ->toBe($location->id);
    });

test('to array',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Address $address */
        $address = Address::factory()
            ->for(Location::factory(), 'addressable')
            ->createQuietly()
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
