<?php

declare(strict_types=1);

use App\Actions\CreateOffice;
use App\Data\CreateAddressData;
use App\Data\CreateOfficeData;
use App\Enums\OfficeType;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(CreateOffice::class);
});

test('creates office with address',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        $addressData = CreateAddressData::from([
            'line1' => '123 Main St',
            'line2' => 'Suite 100',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'United States',
        ]);

        $data = new CreateOfficeData(
            name: 'Headquarters',
            type: OfficeType::Headquarters,
            phone: '+1-555-0100',
            address: $addressData
        );

        $result = $this->action->handle($data, $organization);

        expect($result)
            ->toBeInstanceOf(Office::class)
            ->and($result->name)->toBe('Headquarters')
            ->and($result->type)->toBe(OfficeType::Headquarters)
            ->and($result->phone)->toBe('+1-555-0100')
            ->and($result->organization_id)->toBe($organization->id);

        /** @var Address $address */
        $address = $result->address;

        expect($address)
            ->toBeInstanceOf(Address::class)
            ->and($address->line1)->toBe('123 Main St')
            ->and($address->line2)->toBe('Suite 100')
            ->and($address->city)->toBe('San Francisco')
            ->and($address->state)->toBe('CA')
            ->and($address->postal_code)->toBe('94102')
            ->and($address->country)->toBe('United States');
    });

test('creates office with minimal address',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        $addressData = CreateAddressData::from([
            'line1' => '456 Oak St',
            'line2' => null,
            'city' => 'Portland',
            'state' => null,
            'postal_code' => '97201',
            'country' => 'United States',
        ]);

        $data = new CreateOfficeData(
            name: 'Remote Office',
            type: OfficeType::Remote,
            phone: null,
            address: $addressData
        );

        $result = $this->action->handle($data, $organization);

        expect($result->name)->toBe('Remote Office')
            ->and($result->type)->toBe(OfficeType::Remote)
            ->and($result->phone)->toBeNull();

        /** @var Address $address */
        $address = $result->address;

        expect($address->line2)->toBeNull()
            ->and($address->state)->toBeNull();
    });
