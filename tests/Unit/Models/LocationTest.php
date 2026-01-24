<?php

declare(strict_types=1);

use App\Enums\LocationType;
use App\Models\Address;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

test('location has organization relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        expect($location->organization())
            ->toBeInstanceOf(BelongsTo::class);
    });

test('location has country relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        expect($location->country())
            ->toBeInstanceOf(BelongsTo::class);
    });

test('location has address relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        expect($location->address())
            ->toBeInstanceOf(MorphOne::class);
    });

test('location has employees relationship',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        expect($location->employees())
            ->toBeInstanceOf(HasMany::class);
    });

test('location organization relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Organization $organization */
        $organization = Organization::factory()->createQuietly();

        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'organization_id' => $organization->id,
        ]);

        $location->load('organization');

        expect($location->organization)
            ->toBeInstanceOf(Organization::class)
            ->and($location->organization->id)
            ->toBe($organization->id);
    });

test('location country relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Country $country */
        $country = Country::factory()->createQuietly();

        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'country_id' => $country->id,
        ]);

        $location->load('country');

        expect($location->country)
            ->toBeInstanceOf(Country::class)
            ->and($location->country->id)
            ->toBe($country->id);
    });

test('location address relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->createQuietly();

        $location->load('address');

        expect($location->address)
            ->toBeInstanceOf(Address::class)
            ->and($location->address->id)
            ->toBe($address->id)
            ->and($location->address->addressable_id)
            ->toBe($location->id)
            ->and($location->address->addressable_type)
            ->toBe(Location::class);
    });

test('location employees relationship is properly loaded',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly();

        Employee::factory()
            ->for($location->organization)
            ->createQuietly([
                'location_id' => $location->id,
            ]);

        Employee::factory()
            ->for($location->organization)
            ->createQuietly([
                'location_id' => $location->id,
            ]);

        $location->load('employees');

        expect($location->employees)
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(2);
    });

test('location type is cast to LocationType enum',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'type' => LocationType::Headquarters,
        ]);

        expect($location->type)
            ->toBeInstanceOf(LocationType::class)
            ->and($location->type)
            ->toBe(LocationType::Headquarters)
            ->and($location->type->value)
            ->toBe(1)
            ->and($location->type->label())
            ->toBe('Headquarters');
    });

test('location phone can be null',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()->createQuietly([
            'phone' => null,
        ]);

        expect($location->phone)->toBeNull();
    });

test('to array',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->createQuietly()
            ->refresh();

        expect(array_keys($location->toArray()))
            ->toBe([
                'id',
                'created_at',
                'updated_at',
                'organization_id',
                'country_id',
                'name',
                'type',
                'phone',
            ]);
    });
