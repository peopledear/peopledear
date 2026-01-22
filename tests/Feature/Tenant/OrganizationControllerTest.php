<?php

declare(strict_types=1);

use App\Models\Organization;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('people manager can access organization settings',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $response = $this->actingAs($this->peopleManager)
            ->get(tenant_route('tenant.settings.organization.edit', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('org-settings-general/edit')
                ->has('organization'));
    });

test('owner can access organization settings',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->owner);

        $response = $this->get(tenant_route('tenant.settings.organization.edit', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('org-settings-general/edit')
                ->has('organization'));
    });

test('employee cannot access organization settings',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->employee);

        $response = $this->get(tenant_route('tenant.settings.organization.edit', $this->tenant));

        $response->assertForbidden();
    });

test('people manager can update organization',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->peopleManager);

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => 'Updated Company Name',
            'vat_number' => 'NEW456',
            'ssn' => 'NEW-SSN',
            'phone' => '+9876543210',
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

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

test('owner can update organization',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->owner);

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => 'Owner Updated Name',
            'vat_number' => null,
            'ssn' => null,
            'phone' => null,
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

        /** @var Organization $updatedOrganization */
        $updatedOrganization = $this->organization->fresh();

        expect($updatedOrganization->name)->toBe('Owner Updated Name');
    });

test('employee cannot update organization',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $this->actingAs($this->employee);
        $organizationName = $this->organization->name;

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => 'Hacked Name',
        ]);

        $response->assertForbidden();

        /** @var Organization $unchangedOrganization */
        $unchangedOrganization = $this->organization->fresh();

        expect($unchangedOrganization->name)
            ->toBe($organizationName);
    });

test('requires organization name',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => '',
            'vat_number' => null,
            'ssn' => null,
            'phone' => null,
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('name');
    });

test('validates organization name max length',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => str_repeat('a', 256), // 256 characters - exceeds 255 max
            'vat_number' => null,
            'ssn' => null,
            'phone' => null,
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('name');
    });

test('allows optional vat_number, ssn, and phone',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $this->actingAs($this->peopleManager);

        $response = $this->put(tenant_route('tenant.settings.organization.update', $this->tenant), [
            'name' => 'Minimal Organization',
            'vat_number' => null,
            'ssn' => null,
            'phone' => null,
        ]);

        $response->assertRedirect(tenant_route('tenant.settings.organization.edit', $this->tenant));

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
