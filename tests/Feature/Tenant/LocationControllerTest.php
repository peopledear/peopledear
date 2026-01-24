<?php

declare(strict_types=1);

use App\Actions\Organization\SetCurrentOrganization;
use App\Enums\LocationType;
use App\Models\Address;
use App\Models\Country;
use App\Models\Location;
use Sprout\Exceptions\MisconfigurationException;
use function App\tenant_route;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        resolve(SetCurrentOrganization::class)->handle($this->organization);

        $this->country = Country::factory()->create();
    });

test('people manager can create location with address',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'New York Location',
            'type' => LocationType::Headquarters->value,
            'country_id' => $this->country->id,
            'phone' => '+1234567890',
            'address' => [
                'line1' => '123 Main St',
                'line2' => 'Suite 100',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'United States',
            ],
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location $location */
        $location = Location::query()
            ->where('name', 'New York Location')
            ->first()
            ?->fresh();

        expect($location)
            ->not->toBeNull()
            ->and($location->organization_id)
            ->toBe($this->organization->id)
            ->and($location->name)
            ->toBe('New York Location')
            ->and($location->type)
            ->toBe(LocationType::Headquarters)
            ->and($location->phone)
            ->toBe('+1234567890');

        $address = $location->address;

        expect($address)
            ->not->toBeNull()
            ->and($address->line1)
            ->toBe('123 Main St')
            ->and($address->line2)
            ->toBe('Suite 100')
            ->and($address->city)
            ->toBe('New York')
            ->and($address->state)
            ->toBe('NY')
            ->and($address->postal_code)
            ->toBe('10001')
            ->and($address->country)
            ->toBe('United States');
    });

test('owner can create location with address',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->owner);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'London Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'phone' => null,
            'address' => [
                'line1' => '456 Oxford St',
                'line2' => null,
                'city' => 'London',
                'state' => null,
                'postal_code' => 'W1D 1BS',
                'country' => 'United Kingdom',
            ],
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location $location */
        $location = Location::query()
            ->where('name', 'London Location')
            ->first()
            ?->fresh();

        expect($location)->not->toBeNull();
    });

test('employee cannot create location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->employee);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Unauthorized Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '789 Hack St',
                'city' => 'Hackville',
                'postal_code' => '00000',
                'country' => 'Nowhere',
            ],
        ]);

        $response->assertForbidden();

        /** @var Location|null $location */
        $location = Location::query()
            ->where('name', 'Unauthorized Location')
            ->first();

        expect($location)->toBeNull();
    });

test('people manager can update location and address',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create([
                'name' => 'Old Location Name',
                'type' => LocationType::Branch,
            ]);

        /** @var Address $address */
        $address = Address::factory()
            ->for($location, 'addressable')
            ->create([
                'line1' => 'Old Address',
                'city' => 'Old City',
                'postal_code' => '00000',
                'country' => 'Old Country',
            ]);

        $this->actingAs($this->peopleManager);

        $response = $this->put(tenant_route('tenant.settings.locations.update', $this->tenant, [
            'location' => $location->id,
        ]), [
            'name' => 'Updated Location Name',
            'type' => LocationType::Headquarters->value,
            'country_id' => $this->country->id,
            'phone' => '+9876543210',
            'address' => [
                'line1' => 'New Address',
                'line2' => 'Floor 2',
                'city' => 'New City',
                'state' => 'CA',
                'postal_code' => '90210',
                'country' => 'New Country',
            ],
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location $updatedLocation */
        $updatedLocation = $location->fresh();

        expect($updatedLocation->name)
            ->toBe('Updated Location Name')
            ->and($updatedLocation->type)
            ->toBe(LocationType::Headquarters)
            ->and($updatedLocation->phone)
            ->toBe('+9876543210');

        /** @var Address $updatedAddress */
        $updatedAddress = $address->fresh();

        expect($updatedAddress->line1)
            ->toBe('New Address')
            ->and($updatedAddress->line2)
            ->toBe('Floor 2')
            ->and($updatedAddress->city)
            ->toBe('New City')
            ->and($updatedAddress->state)
            ->toBe('CA')
            ->and($updatedAddress->postal_code)
            ->toBe('90210')
            ->and($updatedAddress->country)
            ->toBe('New Country');
    });

test('owner can update location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create();

        Address::factory()
            ->for($location, 'addressable')
            ->create();

        $this->actingAs($this->owner);

        $response = $this->put(tenant_route('tenant.settings.locations.update', $this->tenant, [
            'location' => $location->id,
        ]), [
            'name' => 'Owner Updated Location',
            'type' => LocationType::Store->value,
            'country_id' => $this->country->id,
            'phone' => null,
            'address' => [
                'line1' => 'Owner Address',
                'line2' => null,
                'city' => 'Owner City',
                'state' => null,
                'postal_code' => '12345',
                'country' => 'Owner Country',
            ],
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location $updatedLocation */
        $updatedLocation = $location->fresh();

        expect($updatedLocation->name)->toBe('Owner Updated Location');
    });

test('employee cannot update location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create([
                'name' => 'Protected Location',
            ]);

        Address::factory()
            ->for($location, 'addressable')
            ->create();

        $this->actingAs($this->employee);

        $response = $this->put(tenant_route('tenant.settings.locations.update', $this->tenant, [
            'location' => $location->id,
        ]), [
            'name' => 'Hacked Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => 'Hack Address',
                'city' => 'Hack City',
                'postal_code' => '99999',
                'country' => 'Hack Country',
            ],
        ]);

        $response->assertForbidden();

        /** @var Location $unchangedLocation */
        $unchangedLocation = $location->fresh();

        expect($unchangedLocation->name)
            ->toBe('Protected Location');
    });

test('people manager can delete location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create();

        $this->actingAs($this->peopleManager);

        $response = $this->delete(tenant_route('tenant.settings.locations.destroy', $this->tenant, [
            'location' => $location->id,
        ]));

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location|null $deletedLocation */
        $deletedLocation = Location::query()
            ->where('id', $location->id)
            ->first();

        expect($deletedLocation)->toBeNull();
    });

test('owner can delete location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create();

        $this->actingAs($this->owner);

        $response = $this->delete(tenant_route('tenant.settings.locations.destroy', $this->tenant, [
            'location' => $location->id,
        ]));

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location|null $deletedLocation */
        $deletedLocation = Location::query()
            ->where('id', $location->id)
            ->first();

        expect($deletedLocation)->toBeNull();
    });

test('employee cannot delete location',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var Location $location */
        $location = Location::factory()
            ->for($this->organization, 'organization')
            ->for($this->country, 'country')
            ->create();

        $this->actingAs($this->employee);

        $response = $this->delete(tenant_route('tenant.settings.locations.destroy', $this->tenant, [
            'location' => $location->id,
        ]));

        $response->assertForbidden();

        /** @var Location $stillExistsLocation */
        $stillExistsLocation = Location::query()
            ->where('id', $location->id)
            ->first()
            ?->fresh();

        expect($stillExistsLocation)->not->toBeNull();
    });

test('requires location name',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => '',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '123 Main St',
                'city' => 'Test City',
                'postal_code' => '12345',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('name');
    });

test('requires location type',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Test Location',
            'type' => null,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '123 Main St',
                'city' => 'Test City',
                'postal_code' => '12345',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('type');
    });

test('requires address line1',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Test Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '',
                'city' => 'Test City',
                'postal_code' => '12345',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('address.line1');
    });

test('requires address city',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Test Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '123 Main St',
                'city' => '',
                'postal_code' => '12345',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('address.city');
    });

test('requires address postal_code',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Test Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '123 Main St',
                'city' => 'Test City',
                'postal_code' => '',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('address.postal_code');
    });

test('requires address country',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Test Location',
            'type' => LocationType::Branch->value,
            'country_id' => $this->country->id,
            'address' => [
                'line1' => '123 Main St',
                'city' => 'Test City',
                'postal_code' => '12345',
                'country' => '',
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('address.country');
    });

test('allows optional address fields',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->peopleManager);

        $response = $this->post(tenant_route('tenant.settings.locations.store', $this->tenant), [
            'name' => 'Minimal Location',
            'type' => LocationType::Remote->value,
            'country_id' => $this->country->id,
            'phone' => null,
            'address' => [
                'line1' => '123 Main St',
                'line2' => null,
                'city' => 'Test City',
                'state' => null,
                'postal_code' => '12345',
                'country' => 'Test Country',
            ],
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Location $location */
        $location = Location::query()
            ->where('name', 'Minimal Location')
            ->first()
            ?->fresh();

        expect($location)
            ->not->toBeNull()
            ->and($location->phone)
            ->toBeNull();

        $address = $location->address;

        expect($address->line2)
            ->toBeNull()
            ->and($address->state)
            ->toBeNull();
    });
