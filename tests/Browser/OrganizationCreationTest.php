<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('owner can create organization via modal', function (): void {
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
    $owner->refresh(); // Ensure role assignment is persisted

    $this->actingAs($owner);

    $page = visit('/org/create');

    $page->assertSee('New organization')
        ->fill('name', 'Test Organization')
        ->click('#country_id')
        ->click(sprintf("[data-slot='select-item']:has-text('%s')", $country->name['EN']))
        ->click('Create organization')
        ->wait(2) // Give time for form submission and redirect in CI
        ->assertNoJavascriptErrors();

    /** @var Organization|null $organization */
    $organization = Organization::query()->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('Test Organization')
        ->country_id->toBe($country->id);
});

test('people manager can create organization', function (): void {
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
    $peopleManager->refresh(); // Ensure role assignment is persisted

    $this->actingAs($peopleManager);

    $page = visit('/org/create');

    $page->assertSee('New organization')
        ->fill('name', 'PM Test Organization')
        ->click('#country_id')
        ->click(sprintf("[data-slot='select-item']:has-text('%s')", $country->name['EN']))
        ->click('Create organization')
        ->wait(2) // Give time for form submission and redirect in CI
        ->assertNoJavascriptErrors();

    /** @var Organization|null $organization */
    $organization = Organization::query()->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('PM Test Organization')
        ->country_id->toBe($country->id);
});

test('employee sees informational page when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $employee */
    $employee = User::factory()->create();
    $employee->assignRole($employeeRole);

    $this->actingAs($employee);

    $page = visit('/dashboard');

    $page->assertPathIs('/organization-required')
        ->assertSee('Organization Not Set Up')
        ->assertSee('An owner or people manager needs to create the organization')
        ->assertNoJavascriptErrors();
});

test('manager sees informational page when no organization exists', function (): void {
    Organization::query()->delete();

    /** @var Role $managerRole */
    $managerRole = Role::query()
        ->where('name', 'manager')
        ->first()
        ?->fresh();

    /** @var User $manager */
    $manager = User::factory()->create();
    $manager->assignRole($managerRole);

    $this->actingAs($manager);

    $page = visit('/dashboard');

    $page->assertPathIs('/organization-required')
        ->assertSee('Organization Not Set Up')
        ->assertSee('An owner or people manager needs to create the organization')
        ->assertNoJavascriptErrors();
});

test('owner can access org overview after organization is created', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create([
        'name' => 'Existing Organization',
    ]);

    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    /** @var User $owner */
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);

    $this->actingAs($owner);

    $page = visit('/org');

    $page->assertPathIs('/org')
        ->assertDontSee('New organization')
        ->assertNoJavascriptErrors();
});
