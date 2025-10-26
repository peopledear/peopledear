<?php

declare(strict_types=1);

use App\Actions\DeleteAddress;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(DeleteAddress::class);
});

test('deletes address of addressable model',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Office $office */
        $office = Office::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($office, 'addressable')
            ->createQuietly();

        $addressId = $address->id;

        $this->action->handle($office);

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

        /** @var Office $office */
        $office = Office::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        // No address created - should not throw exception
        $this->action->handle($office);

        expect($office->address)->toBeNull();
    });
