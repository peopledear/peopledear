<?php

declare(strict_types=1);

use App\Actions\CreateAddress;
use App\Data\CreateAddressData;
use App\Models\Address;
use App\Models\Office;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = app(CreateAddress::class);
});

test('creates address for addressable model',
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

        $data = CreateAddressData::from([
            'line1' => '123 Main Street',
            'line2' => 'Suite 100',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'United States',
        ]);

        $result = $this->action->handle($office, $data);

        expect($result)
            ->toBeInstanceOf(Address::class)
            ->and($result->addressable_id)->toBe($office->id)
            ->and($result->addressable_type)->toBe(Office::class)
            ->and($result->line1)->toBe('123 Main Street')
            ->and($result->line2)->toBe('Suite 100')
            ->and($result->city)->toBe('San Francisco')
            ->and($result->state)->toBe('CA')
            ->and($result->postal_code)->toBe('94102')
            ->and($result->country)->toBe('United States');
    });

test('creates address with minimal fields',
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

        $data = CreateAddressData::from([
            'line1' => '456 Oak Ave',
            'line2' => null,
            'city' => 'Portland',
            'state' => null,
            'postal_code' => '97201',
            'country' => 'United States',
        ]);

        $result = $this->action->handle($office, $data);

        expect($result->line1)->toBe('456 Oak Ave')
            ->and($result->line2)->toBeNull()
            ->and($result->city)->toBe('Portland')
            ->and($result->state)->toBeNull()
            ->and($result->postal_code)->toBe('97201')
            ->and($result->country)->toBe('United States');
    });
