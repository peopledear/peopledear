<?php

declare(strict_types=1);

use App\Actions\Location\UpdateLocation;
use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Data\PeopleDear\Location\UpdateLocationData;
use App\Enums\LocationType;
use App\Exceptions\Domain\LocationAlreadyExistsException;
use App\Models\Address;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(UpdateLocation::class);
    $this->country = Country::factory()
        ->create(['name' => 'United States']);
});

test('updates office with all fields',
    /**
     * @throws Throwable
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Location $location */
        $location = Location::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Old Name',
            'type' => LocationType::Branch,
            'phone' => 'Old Phone',
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->create([
                'line1' => 'Old Street',
                'city' => 'Old City',
                'postal_code' => '00000',
                'country' => 'Old Country',
            ]);

        $addressData = UpdateAddressData::from([
            'line1' => 'New Street',
            'line2' => 'Floor 5',
            'city' => 'New City',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
        ]);

        $data = new UpdateLocationData(
            name: 'New Name',
            type: LocationType::Headquarters,
            countryId: $this->country->id,
            phone: '+1-555-1234',
            address: $addressData
        );

        $result = $this->action->handle($location, $data);

        expect($result->name)
            ->toBe('New Name')
            ->and($result->type)
            ->toBe(LocationType::Headquarters)
            ->and($result->phone)
            ->toBe('+1-555-1234');

        /** @var Address $updatedAddress */
        $updatedAddress = $address->fresh();

        expect($updatedAddress->line1)
            ->toBe('New Street')
            ->and($updatedAddress->city)
            ->toBe('New City')
            ->and($updatedAddress->postal_code)
            ->toBe('10001');
    });

test('updates office without updating address when not provided',
    /**
     * @throws Throwable
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Location $location */
        $location = Location::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Old Name',
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->create([
                'line1' => 'Original Street',
                'city' => 'Original City',
                'postal_code' => '12345',
                'country' => 'Original Country',
            ]);

        $data = UpdateLocationData::from([
            'name' => 'Updated Name',
        ]);

        $result = $this->action->handle($location, $data);

        expect($result->name)->toBe('Updated Name');

        /** @var Address $unchangedAddress */
        $unchangedAddress = $address->fresh();

        expect($unchangedAddress->line1)
            ->toBe('Original Street')
            ->and($unchangedAddress->city)
            ->toBe('Original City');
    });

test('throws exception when changing to headquarters and one already exists in country',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        Location::factory()->createQuietly([
            'organization_id' => $organization->id,
            'type' => LocationType::Headquarters,
            'country_id' => $this->country->id,
        ]);

        /** @var Location $branchLocation */
        $branchLocation = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
            'type' => LocationType::Branch,
            'country_id' => $this->country->id,
        ]);

        $data = UpdateLocationData::from([
            'type' => LocationType::Headquarters,
        ]);

        $this->action->handle($branchLocation, $data);
    })->throws(LocationAlreadyExistsException::class);

test('throws exception when changing headquarters country to one that already has headquarters',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Country $targetCountry */
        $targetCountry = Country::factory()->createQuietly(['name' => 'Canada']);

        /** @var Location $existingHeadquarters */
        $existingHeadquarters = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
            'type' => LocationType::Headquarters,
            'country_id' => $targetCountry->id,
        ]);

        /** @var Location $currentHeadquarters */
        $currentHeadquarters = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
            'type' => LocationType::Headquarters,
            'country_id' => $this->country->id,
        ]);

        $data = UpdateLocationData::from([
            'country_id' => $targetCountry->id,
        ]);

        $this->action->handle($currentHeadquarters, $data);
    })->throws(LocationAlreadyExistsException::class);
