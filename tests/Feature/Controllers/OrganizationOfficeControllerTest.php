<?php

declare(strict_types=1);

use App\Enums\PeopleDear\OfficeType;
use App\Models\Address;
use App\Models\Office;

test('people manager can create office with address', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'New York Office',
        'type' => OfficeType::Headquarters->value,
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

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office $office */
    $office = Office::query()
        ->where('name', 'New York Office')
        ->first()
        ?->fresh();

    expect($office)
        ->not->toBeNull()
        ->and($office->organization_id)
        ->toBe($this->organization->id)
        ->and($office->name)
        ->toBe('New York Office')
        ->and($office->type)
        ->toBe(OfficeType::Headquarters)
        ->and($office->phone)
        ->toBe('+1234567890');

    $address = $office->address;

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

test('owner can create office with address', function (): void {

    $this->actingAs($this->owner);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'London Office',
        'type' => OfficeType::Branch->value,
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

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office $office */
    $office = Office::query()
        ->where('name', 'London Office')
        ->first()
        ?->fresh();

    expect($office)->not->toBeNull();
});

test('employee cannot create office', function (): void {
    $this->actingAs($this->employee);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Unauthorized Office',
        'type' => OfficeType::Branch->value,
        'address' => [
            'line1' => '789 Hack St',
            'city' => 'Hackville',
            'postal_code' => '00000',
            'country' => 'Nowhere',
        ],
    ]);

    $response->assertForbidden();

    /** @var Office|null $office */
    $office = Office::query()
        ->where('name', 'Unauthorized Office')
        ->first();

    expect($office)->toBeNull();
});

test('people manager can update office and address', function (): void {

    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create([
            'name' => 'Old Office Name',
            'type' => OfficeType::Branch,
        ]);

    /** @var Address $address */
    $address = Address::factory()
        ->for($office, 'addressable')
        ->create([
            'line1' => 'Old Address',
            'city' => 'Old City',
            'postal_code' => '00000',
            'country' => 'Old Country',
        ]);

    $this->actingAs($this->peopleManager);

    $response = $this->put(route('org.offices.update', $office), [
        'name' => 'Updated Office Name',
        'type' => OfficeType::Headquarters->value,
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

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office $updatedOffice */
    $updatedOffice = $office->fresh();

    expect($updatedOffice->name)
        ->toBe('Updated Office Name')
        ->and($updatedOffice->type)
        ->toBe(OfficeType::Headquarters)
        ->and($updatedOffice->phone)
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

test('owner can update office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create();

    Address::factory()
        ->for($office, 'addressable')
        ->create();

    $this->actingAs($this->owner);

    $response = $this->put(route('org.offices.update', $office), [
        'name' => 'Owner Updated Office',
        'type' => OfficeType::Store->value,
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

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office $updatedOffice */
    $updatedOffice = $office->fresh();

    expect($updatedOffice->name)->toBe('Owner Updated Office');
});

test('employee cannot update office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create([
            'name' => 'Protected Office',
        ]);

    Address::factory()
        ->for($office, 'addressable')
        ->create();

    $this->actingAs($this->employee);

    $response = $this->put(route('org.offices.update', $office), [
        'name' => 'Hacked Office',
        'type' => OfficeType::Branch->value,
        'address' => [
            'line1' => 'Hack Address',
            'city' => 'Hack City',
            'postal_code' => '99999',
            'country' => 'Hack Country',
        ],
    ]);

    $response->assertForbidden();

    /** @var Office $unchangedOffice */
    $unchangedOffice = $office->fresh();

    expect($unchangedOffice->name)
        ->toBe('Protected Office');
});

test('people manager can delete office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create();

    $this->actingAs($this->peopleManager);

    $response = $this->delete(route('org.offices.destroy', $office));

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office|null $deletedOffice */
    $deletedOffice = Office::query()
        ->where('id', $office->id)
        ->first();

    expect($deletedOffice)->toBeNull();
});

test('owner can delete office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create();

    $this->actingAs($this->owner);

    $response = $this->delete(route('org.offices.destroy', $office));

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office|null $deletedOffice */
    $deletedOffice = Office::query()
        ->where('id', $office->id)
        ->first();

    expect($deletedOffice)->toBeNull();
});

test('employee cannot delete office', function (): void {
    /** @var Office $office */
    $office = Office::factory()
        ->for($this->organization, 'organization')
        ->create();

    $this->actingAs($this->employee);

    $response = $this->delete(route(
        'org.offices.destroy',
        $office
    ));

    $response->assertForbidden();

    /** @var Office $stillExistsOffice */
    $stillExistsOffice = Office::query()
        ->where('id', $office->id)
        ->first()
        ?->fresh();

    expect($stillExistsOffice)->not->toBeNull();
});

test('requires office name', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => '',
        'type' => OfficeType::Branch->value,
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

test('requires office type', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Test Office',
        'type' => null,
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

test('requires address line1', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Test Office',
        'type' => OfficeType::Branch->value,
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

test('requires address city', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Test Office',
        'type' => OfficeType::Branch->value,
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

test('requires address postal_code', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Test Office',
        'type' => OfficeType::Branch->value,
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

test('requires address country', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Test Office',
        'type' => OfficeType::Branch->value,
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

test('allows optional address fields', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.offices.store'), [
        'name' => 'Minimal Office',
        'type' => OfficeType::Remote->value,
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

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Office $office */
    $office = Office::query()
        ->where('name', 'Minimal Office')
        ->first()
        ?->fresh();

    expect($office)
        ->not->toBeNull()
        ->and($office->phone)
        ->toBeNull();

    $address = $office->address;

    expect($address->line2)
        ->toBeNull()
        ->and($address->state)
        ->toBeNull();
});
