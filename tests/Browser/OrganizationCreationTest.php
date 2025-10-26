<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('owner can create organization via modal', function (): void {
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

    $page = visit('/org');

    $page->assertSee('New organization')
        ->fill('name', 'Test Organization')
        ->click('Create organization')
        ->assertPathIs('/org')
        ->assertNoJavascriptErrors();

    /** @var Organization|null $organization */
    $organization = Organization::query()->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('Test Organization');
});

test('people manager can create organization', function (): void {
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

    $page = visit('/org');

    $page->assertSee('New organization')
        ->fill('name', 'PM Test Organization')
        ->click('Create organization')
        ->assertPathIs('/org')
        ->assertNoJavascriptErrors();

    /** @var Organization|null $organization */
    $organization = Organization::query()->first();

    expect($organization)
        ->not->toBeNull()
        ->name->toBe('PM Test Organization');
});

test('employee sees informational page when no organization exists', function (): void {
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
    $manager = User::factory()->createQuietly();
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
    $organization = Organization::factory()->createQuietly([
        'name' => 'Existing Organization',
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

    $page = visit('/org');

    $page->assertPathIs('/org')
        ->assertDontSee('New organization')
        ->assertNoJavascriptErrors();
});
