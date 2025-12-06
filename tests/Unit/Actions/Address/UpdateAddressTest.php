<?php

declare(strict_types=1);

use App\Actions\Address\UpdateAddress;
use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(UpdateAddress::class);
});

test('updates address with all fields',
    /**
     * @throws Exception
     */
    function (): void {

        /** @var Organization $organization */
        $organization = Organization::factory()
            ->create();

        /** @var Office $office */
        $office = Office::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Address::factory()
            ->for($office, 'addressable')
            ->create([
                'line1' => 'Old Street',
                'line2' => 'Old Suite',
                'city' => 'Old City',
                'state' => 'Old State',
                'postal_code' => '00000',
                'country' => 'Old Country',
            ]);

        $data = UpdateAddressData::from([
            'line1' => 'New Street',
            'line2' => 'New Suite',
            'city' => 'New City',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
        ]);

        $result = $this->action->handle($office, $data);

        expect($result->line1)->toBe('New Street')
            ->and($result->line2)->toBe('New Suite')
            ->and($result->city)->toBe('New City')
            ->and($result->state)->toBe('NY')
            ->and($result->postal_code)->toBe('10001')
            ->and($result->country)->toBe('United States');
    });

test('updates address with partial fields',
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

        Address::factory()
            ->for($office, 'addressable')
            ->create([
                'line1' => 'Original Street',
                'city' => 'Original City',
                'postal_code' => '12345',
                'country' => 'Original Country',
            ]);

        $data = UpdateAddressData::from([
            'city' => 'Updated City',
        ]);

        $result = $this->action->handle($office, $data);

        expect($result->line1)->toBe('Original Street')
            ->and($result->city)->toBe('Updated City')
            ->and($result->postal_code)->toBe('12345')
            ->and($result->country)->toBe('Original Country');
    });

test('can set fields to null explicitly',
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

        Address::factory()
            ->for($office, 'addressable')
            ->create([
                'line1' => 'Street',
                'line2' => 'Suite 200',
                'city' => 'City',
                'state' => 'CA',
                'postal_code' => '12345',
                'country' => 'Country',
            ]);

        $data = UpdateAddressData::from([
            'line2' => null,
            'state' => null,
        ]);

        $result = $this->action->handle($office, $data);

        expect($result->line1)->toBe('Street')
            ->and($result->line2)->toBeNull()
            ->and($result->state)->toBeNull()
            ->and($result->city)->toBe('City');
    });
