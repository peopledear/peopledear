<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
    ]);

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();
    /** @var Role $ownerRole */
    $ownerRole = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();
    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    /** @var User $peopleManager */
    $peopleManager = User::factory()->create();
    $peopleManager->assignRole($peopleManagerRole);
    $this->peopleManager = $peopleManager;

    /** @var User $owner */
    $owner = User::factory()->create();
    $owner->assignRole($ownerRole);
    $this->owner = $owner;

    /** @var User $employee */
    $employee = User::factory()->create();
    $employee->assignRole($employeeRole);
    $this->employee = $employee;
});

test('admin layout renders for people manager role', function (): void {
    actingAs($this->peopleManager);

    $page = visit(route('org.overview'));

    $page->assertSee('Overview')
        ->assertNoJavascriptErrors();
});

test('admin layout renders for owner role', function (): void {
    actingAs($this->owner);

    $page = visit(route('org.overview'));

    $page->assertSee('Overview')
        ->assertNoJavascriptErrors();
});

test('admin layout redirects employee role', function (): void {
    actingAs($this->employee);

    $page = visit(route('org.overview'));

    $page->assertSee('403')
        ->assertNoJavascriptErrors();
});

test('admin navigation menu displays correct items', function (): void {
    actingAs($this->peopleManager);

    $page = visit(route('org.overview'));

    $page->assertSee('Overview')
        ->assertSee('Settings')
        ->assertNoJavascriptErrors();
});

test('mobile navigation works on small screens', function (): void {
    actingAs($this->owner);

    $page = visit(route('org.overview'))
        ->resize(375, 667);

    $page->assertNoJavascriptErrors();
});
