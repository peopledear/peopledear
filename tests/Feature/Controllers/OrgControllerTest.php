<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('people manager can access organization settings', function (): void {
    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $this->actingAs($peopleManager);

    $response = $this->get(route('org.settings.organization.edit'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

it('owner can access organization settings', function (): void {
    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $this->actingAs($owner);

    $response = $this->get(route('org.settings.organization.edit'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-settings-general/edit')
            ->has('organization'));
});

it('employee cannot access organization settings', function (): void {
    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $employee */
    $employee = User::factory()->createQuietly();
    $employee->assignRole($employeeRole);

    $this->actingAs($employee);

    $response = $this->get(route('org.settings.organization.edit'));

    $response->assertForbidden();
});

it('people manager can update organization', function (): void {
    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Company Name',
        'vat_number' => 'OLD123',
        'ssn' => 'OLD-SSN',
        'phone' => '+1234567890',
    ]);

    $this->actingAs($peopleManager);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => 'Updated Company Name',
        'vat_number' => 'NEW456',
        'ssn' => 'NEW-SSN',
        'phone' => '+9876543210',
    ]);

    $response->assertRedirect(route('org.settings.organization.edit'));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $organization->fresh();

    expect($updatedOrganization->name)
        ->toBe('Updated Company Name')
        ->and($updatedOrganization->vat_number)
        ->toBe('NEW456')
        ->and($updatedOrganization->ssn)
        ->toBe('NEW-SSN')
        ->and($updatedOrganization->phone)
        ->toBe('+9876543210');
});

it('owner can update organization', function (): void {
    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Company Name',
    ]);

    $this->actingAs($owner);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => 'Owner Updated Name',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('org.settings.organization.edit'));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $organization->fresh();

    expect($updatedOrganization->name)->toBe('Owner Updated Name');
});

it('employee cannot update organization', function (): void {
    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $employee */
    $employee = User::factory()->createQuietly();
    $employee->assignRole($employeeRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Original Name',
    ]);

    $this->actingAs($employee);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();

    /** @var Organization $unchangedOrganization */
    $unchangedOrganization = $organization->fresh();

    expect($unchangedOrganization->name)->toBe('Original Name');
});

it('requires organization name', function (): void {
    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    Organization::factory()->createQuietly();

    $this->actingAs($peopleManager);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => '',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('name');
});

it('validates organization name max length', function (): void {
    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    Organization::factory()->createQuietly();

    $this->actingAs($peopleManager);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => str_repeat('a', 256), // 256 characters - exceeds 255 max
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('name');
});

it('allows optional vat_number, ssn, and phone', function (): void {
    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $this->actingAs($peopleManager);

    $response = $this->put(route('org.settings.organization.update'), [
        'name' => 'Minimal Organization',
        'vat_number' => null,
        'ssn' => null,
        'phone' => null,
    ]);

    $response->assertRedirect(route('org.settings.organization.edit'));

    /** @var Organization $updatedOrganization */
    $updatedOrganization = $organization->fresh();

    expect($updatedOrganization->name)
        ->toBe('Minimal Organization')
        ->and($updatedOrganization->vat_number)
        ->toBeNull()
        ->and($updatedOrganization->ssn)
        ->toBeNull()
        ->and($updatedOrganization->phone)
        ->toBeNull();
});
