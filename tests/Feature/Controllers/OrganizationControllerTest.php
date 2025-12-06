<?php

declare(strict_types=1);

use App\Models\Country;
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
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    Organization::factory()->create();

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
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    Organization::factory()->create();

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
    $employee = User::factory()->create();
    $employee->assignRole($employeeRole);

    Organization::factory()->create();

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
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->create([
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
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->create([
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
    $employee = User::factory()->create();
    $employee->assignRole($employeeRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->create([
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
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    Organization::factory()->create();

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
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    Organization::factory()->create();

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
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    /** @var Organization $organization */
    $organization = Organization::factory()->create();

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

it('requires country_id when creating organization', function (): void {
    Organization::query()->delete();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $response = $this->post(route('org.create'), [
        'name' => 'Test Organization',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('country_id');
});

it('validates country_id exists when creating organization', function (): void {
    Organization::query()->delete();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $response = $this->post(route('org.create'), [
        'name' => 'Test Organization',
        'country_id' => 99999, // Non-existent country ID
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors('country_id');
});

it('people manager can create organization with country', function (): void {
    Organization::query()->delete();

    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);

    $this->actingAs($peopleManager);

    $response = $this->post(route('org.create'), [
        'name' => 'New Organization',
        'country_id' => $country->id,
    ]);

    $response->assertRedirect(route('org.overview'));

    /** @var Organization $organization */
    $organization = Organization::query()
        ->where('name', 'New Organization')
        ->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('New Organization')
        ->country_id->toBe($country->id);
});

it('owner can create organization with country', function (): void {
    Organization::query()->delete();

    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

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
