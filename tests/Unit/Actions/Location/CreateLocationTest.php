<?php

declare(strict_types=1);

use App\Actions\Location\CreateLocation;
use App\Data\PeopleDear\Address\CreateAddressData;
use App\Data\PeopleDear\Location\CreateLocationData;
use App\Enums\LocationType;
use App\Models\Address;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(CreateLocation::class);

    $this->organization = Organization::factory()
        ->create();

    $this->country = Country::factory()
        ->create(['name' => 'United States']);
});

test('creates office with address',
    /**
     * @throws Throwable
     */
    function (): void {

        $addressData = CreateAddressData::from([
            'line1' => '123 Main St',
            'line2' => 'Suite 100',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'United States',
        ]);

        $data = new CreateLocationData(
            name: 'Headquarters',
            type: LocationType::Headquarters,
            countryId: $this->country->id,
            phone: '+1-555-0100',
            address: $addressData,
        );

        $result = $this->action->handle(
            organization: $this->organization,
            data: $data
        );

        expect($result)
            ->toBeInstanceOf(Location::class)
            ->and($result->name)
            ->toBe('Headquarters')
            ->and($result->type)
            ->toBe(LocationType::Headquarters)
            ->and($result->phone)
            ->toBe('+1-555-0100')
            ->and($result->organization_id)
            ->toBe($this->organization->id);

        /** @var Address $address */
        $address = $result->address;

        expect($address)
            ->toBeInstanceOf(Address::class)
            ->and($address->line1)
            ->toBe('123 Main St')
            ->and($address->line2)
            ->toBe('Suite 100')
            ->and($address->city)
            ->toBe('San Francisco')
            ->and($address->state)
            ->toBe('CA')
            ->and($address->postal_code)
            ->toBe('94102')
            ->and($address->country)
            ->toBe('United States');
    });

test('creates office with minimal address',
    /**
     * @throws Throwable
     */
    function (): void {

        $addressData = CreateAddressData::from([
            'line1' => '456 Oak St',
            'line2' => null,
            'city' => 'Portland',
            'state' => null,
            'postal_code' => '97201',
            'country' => 'United States',
        ]);

        $data = new CreateLocationData(
            name: 'Remote Office',
            type: LocationType::Remote,
            countryId: $this->country->id,
            phone: null,
            address: $addressData
        );

        $result = $this->action->handle(
            organization: $this->organization,
            data: $data
        );

        expect($result->name)
            ->toBe('Remote Office')
            ->and($result->type)
            ->toBe(LocationType::Remote)
            ->and($result->phone)
            ->toBeNull();

        /** @var Address $address */
        $address = $result->address;

        expect($address->line2)
            ->toBeNull()
            ->and($address->state)
            ->toBeNull();
    });
