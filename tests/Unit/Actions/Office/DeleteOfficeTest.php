<?php

declare(strict_types=1);

use App\Actions\Office\DeleteOffice;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(DeleteOffice::class);
});

test('deletes office and its address',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Office $office */
        $office = Office::factory()->create([
            'organization_id' => $organization->id,
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($office, 'addressable')
            ->create();

        $officeId = $office->id;
        $addressId = $address->id;

        $this->action->handle($office);

        /** @var Office|null $deletedOffice */
        $deletedOffice = Office::query()->find($officeId);

        /** @var Address|null $deletedAddress */
        $deletedAddress = Address::query()->find($addressId);

        expect($deletedOffice)->toBeNull()
            ->and($deletedAddress)->toBeNull();
    });

test('deletes office without address gracefully',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Office $office */
        $office = Office::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $officeId = $office->id;

        $this->action->handle($office);

        /** @var Office|null $deletedOffice */
        $deletedOffice = Office::query()->find($officeId);

        expect($deletedOffice)->toBeNull();
    });
