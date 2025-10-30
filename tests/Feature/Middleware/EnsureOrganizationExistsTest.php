<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('middleware redirects owner to org create when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $response = $this->get('/dashboard');

    $response->assertRedirect('/org/create');
});

test('middleware redirects people manager to org create when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->createQuietly();
    $peopleManager->assignRole($peopleManagerRole);

    $this->actingAs($peopleManager);

    $response = $this->get('/dashboard');

    $response->assertRedirect('/org/create');
});

test('middleware redirects employee to organization-required when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $employee */
    $employee = User::factory()->createQuietly();
    $employee->assignRole($employeeRole);

    $this->actingAs($employee);

    $response = $this->get('/dashboard');

    $response->assertRedirect('/organization-required');
});

test('middleware redirects manager to organization-required when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $managerRole */
    $managerRole = Role::query()
        ->where('name', 'manager')
        ->first()
        ?->fresh();

    /** @var User $manager */
    $manager = User::factory()->createQuietly();
    $manager->assignRole($managerRole);

    $this->actingAs($manager);

    $response = $this->get('/dashboard');

    $response->assertRedirect('/organization-required');
});

test('middleware allows access when organization exists', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Organization',
    ]);

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $response = $this->get('/dashboard');

    $response->assertOk();
});

test('middleware caches organization ID in session', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Organization',
    ]);

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    expect(session(SessionKey::CurrentOrganization->value))->toBeNull();

    $this->get('/dashboard');

    expect(session(SessionKey::CurrentOrganization->value))->toBe($organization->id);
});

test('middleware does not redirect on org create route', function (): void {
    Organization::query()->delete();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $response = $this->get('/org/create');

    $response->assertOk();
});

test('middleware does not redirect on organization-required route', function (): void {
    Organization::query()->delete();

    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $employee */
    $employee = User::factory()->createQuietly();
    $employee->assignRole($employeeRole);

    $this->actingAs($employee);

    $response = $this->get('/organization-required');

    $response->assertOk();
});

test('creating organization sets session cache', function (): void {
    Organization::query()->delete();

    /** @var Country $country */
    $country = Country::factory()->createQuietly();

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->createQuietly();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    expect(session(SessionKey::CurrentOrganization->value))->toBeNull();

    $response = $this->post('/org/create', [
        'name' => 'New Organization',
        'country_id' => $country->id,
    ]);

    $response->assertRedirect('/org');

    expect(session(SessionKey::CurrentOrganization->value))->toBeInt();

    /** @var Organization $organization */
    $organization = Organization::query()->where('name', 'New Organization')->first();

    expect(session(SessionKey::CurrentOrganization->value))->toBe($organization->id);
});
