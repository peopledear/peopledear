<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Organization;

test('people manager can access organization settings', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->get(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

test('owner can access organization settings', function (): void {
    $this->actingAs($this->owner);

    $response = $this->get(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

test('employee cannot access organization settings', function (): void {

    $this->actingAs($this->employee);

    $response = $this->get(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    $response->assertForbidden();
});

test('people manager can update organization', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => 'Updated Company Name',
        'vat_number' => 'NEW456',
        'ssn' => 'NEW-SSN',
        'phone' => '+9876543210',
    ]);

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $this->organization->fresh();

    expect($updatedOrganization->name)
        ->toBe('Updated Company Name')
        ->and($updatedOrganization->vat_number)
        ->toBe('NEW456')
        ->and($updatedOrganization->ssn)
        ->toBe('NEW-SSN')
        ->and($updatedOrganization->phone)
        ->toBe('+9876543210');
});

test('owner can update organization', function (): void {

    $this->actingAs($this->owner);

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => 'Owner Updated Name',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $this->organization->fresh();

    expect($updatedOrganization->name)->toBe('Owner Updated Name');
});

test('employee cannot update organization', function (): void {

    $this->actingAs($this->employee);
    $organizationName = $this->organization->name;

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();

    /** @var Organization $unchangedOrganization */
    $unchangedOrganization = $this->organization->fresh();

    expect($unchangedOrganization->name)
        ->toBe($organizationName);
});

test('requires organization name', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => '',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('name');
});

test('validates organization name max length', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => str_repeat('a', 256), // 256 characters - exceeds 255 max
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('name');
});

test('allows optional vat_number, ssn, and phone', function (): void {
    $this->actingAs($this->peopleManager);

    $response = $this->put(route('org.settings.organization.update', [
        'organization' => $this->organization->id,
    ]), [
        'name' => 'Minimal Organization',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('org.settings.organization.edit', [
        'organization' => $this->organization->id,
    ]));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $this->organization->fresh();

    expect($updatedOrganization->name)
        ->toBe('Minimal Organization')
        ->and($updatedOrganization->vat_number)
        ->toBeNull()
        ->and($updatedOrganization->ssn)
        ->toBeNull()
        ->and($updatedOrganization->phone)
        ->toBeNull();
});

test('requires country_id when creating organization', function (): void {

    $this->actingAs($this->owner);

    $response = $this->post(route('org.create'), [
        'name' => 'Test Organization',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('country_id');
});

test('validates country_id exists when creating organization', function (): void {
    Organization::query()->delete();

    $this->actingAs($this->owner);

    $response = $this->post(route('org.create'), [
        'name' => 'Test Organization',
        'country_id' => 99999,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('country_id');
});

test('people manager cannot create organization with country', function (): void {
    Organization::query()->delete();

    /** @var Country $country */
    $country = Country::factory()->create();

    $this->actingAs($this->peopleManager);

    $response = $this->post(route('org.create'), [
        'name' => 'New Organization',
        'country_id' => $country->id,
    ]);

    $response->assertForbidden();

});

test('owner can create organization with country', function (): void {
    Organization::query()->delete();

    /** @var Country $country */
    $country = Country::factory()->create();

    $this->actingAs($this->owner);

    $response = $this->post(route('org.create'), [
        'name' => 'Owner Organization',
        'country_id' => $country->id,
    ]);

    $response->assertRedirect(route('org.overview'));

    /** @var Organization $organization */
    $organization = Organization::query()
        ->where('name', 'Owner Organization')
        ->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('Owner Organization')
        ->country_id->toBe($country->id);
});
