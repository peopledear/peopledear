<?php

declare(strict_types=1);

use App\Actions\Address\DeleteAddress;
use App\Models\Address;
use App\Models\Location;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(DeleteAddress::class);
});

test('deletes address of addressable model',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->createQuietly();

        $addressId = $address->id;

        $this->action->handle($location);

        /** @var Address|null $deletedAddress */
        $deletedAddress = Address::query()->find($addressId);

        expect($deletedAddress)->toBeNull();
    });

test('handles addressable without address gracefully',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        // No address created - should not throw exception
        $this->action->handle($location);

        expect($location->address)->toBeNull();
    });
