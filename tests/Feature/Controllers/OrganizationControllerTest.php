<?php

declare(strict_types=1);

use App\Models\Organization;

test('people manager can access organization settings', function (): void {

    $response = $this->actingAs($this->peopleManager)->get(route(
        'tenant.settings.organization.edit', [
            'tenant' => $this->peopleManager->organization->identifier,
        ]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

test('owner can access organization settings', function (): void {
    $this->actingAs($this->owner);

    $response = $this->get(route('tenant.settings.organization.edit', [
        'tenant' => $this->owner->organization->identifier,
    ]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

test('employee cannot access organization settings', function (): void {

    $this->actingAs($this->employee);

    $response = $this->get(route('tenant.settings.organization.edit', [
        'tenant' => $this->employee->organization->identifier,
    ]));

    $response->assertForbidden();
});

test('people manager can update organization', function (): void {

    $this->actingAs($this->peopleManager);

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->peopleManager->organization->identifier,
    ]), [
        'name' => 'Updated Company Name',
        'vat_number' => 'NEW456',
        'ssn' => 'NEW-SSN',
        'phone' => '+9876543210',
    ]);

    $response->assertRedirect(route('tenant.settings.organization.edit', [
        'tenant' => $this->peopleManager->organization->identifier,
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

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->owner->organization->identifier,
    ]), [
        'name' => 'Owner Updated Name',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('tenant.settings.organization.edit', [
        'tenant' => $this->owner->organization->identifier,
    ]));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $this->organization->fresh();

    expect($updatedOrganization->name)->toBe('Owner Updated Name');
});

test('employee cannot update organization', function (): void {

    $this->actingAs($this->employee);
    $organizationName = $this->organization->name;

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->employee->organization->identifier,
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

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->peopleManager->organization->identifier,
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

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->peopleManager->organization->identifier,
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

    $response = $this->put(route('tenant.settings.organization.update', [
        'tenant' => $this->peopleManager->organization->identifier,
    ]), [
        'name' => 'Minimal Organization',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('tenant.settings.organization.edit', [
        'tenant' => $this->peopleManager->organization->identifier,
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
