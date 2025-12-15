<?php

declare(strict_types=1);

use App\Actions\Office\UpdateOffice;
use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Data\PeopleDear\Office\UpdateOfficeData;
use App\Enums\PeopleDear\OfficeType;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(UpdateOffice::class);
});

test('updates office with all fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Office $office */
        $office = Office::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Old Name',
            'type' => OfficeType::Branch,
            'phone' => 'Old Phone',
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($office, 'addressable')
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

        $data = new UpdateOfficeData(
            name: 'New Name',
            type: OfficeType::Headquarters,
            phone: '+1-555-1234',
            address: $addressData
        );

        $result = $this->action->handle($office, $data);

        expect($result->name)->toBe('New Name')
            ->and($result->type)->toBe(OfficeType::Headquarters)
            ->and($result->phone)->toBe('+1-555-1234');

        /** @var Address $updatedAddress */
        $updatedAddress = $address->fresh();

        expect($updatedAddress->line1)->toBe('New Street')
            ->and($updatedAddress->city)->toBe('New City')
            ->and($updatedAddress->postal_code)->toBe('10001');
    });

test('updates office without updating address when not provided',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->create();

        /** @var Office $office */
        $office = Office::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Old Name',
        ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($office, 'addressable')
            ->create([
                'line1' => 'Original Street',
                'city' => 'Original City',
                'postal_code' => '12345',
                'country' => 'Original Country',
            ]);

        $data = UpdateOfficeData::from([
            'name' => 'Updated Name',
        ]);

        $result = $this->action->handle($office, $data);

        expect($result->name)->toBe('Updated Name');

        /** @var Address $unchangedAddress */
        $unchangedAddress = $address->fresh();

        expect($unchangedAddress->line1)->toBe('Original Street')
            ->and($unchangedAddress->city)->toBe('Original City');
    });
