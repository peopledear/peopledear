<?php

declare(strict_types=1);

use App\Actions\Location\DeleteLocation;
use App\Models\Address;
use App\Models\Location;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(DeleteLocation::class);
});

test('deletes office and its address',
    /**
     * @throws Throwable
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Location $location */
        $location = Location::factory()->create([
            'organization_id' => $organization->id,
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->create();

        $locationId = $location->id;
        $addressId = $address->id;

        $this->action->handle($location);

        /** @var Location|null $deletedLocation */
        $deletedLocation = Location::query()
            ->find($locationId);

        /** @var Address|null $deletedAddress */
        $deletedAddress = Address::query()->find($addressId);

        expect($deletedLocation)
            ->toBeNull()
            ->and($deletedAddress)
            ->toBeNull();
    });

test('deletes office without address gracefully',
    /**
     * @throws Throwable
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Location $location */
        $location = Location::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $locationId = $location->id;

        $this->action->handle($location);

        /** @var Location|null $deletedLocation */
        $deletedLocation = Location::query()->find($locationId);

        expect($deletedLocation)->toBeNull();
    });
