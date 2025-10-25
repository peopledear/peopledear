<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Role;

test('user can be assigned a role', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasRole('employee'))->toBeTrue();
});

test('user can have multiple roles', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $employeeRole */
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();
    /** @var Role $managerRole */
    $managerRole = Role::query()
        ->where('name', 'manager')
        ->first()
        ?->fresh();

    $user->assignRole([$employeeRole, $managerRole]);

    expect($user->hasRole('employee'))
        ->toBeTrue()
        ->and($user->hasRole('manager'))
        ->toBeTrue()
        ->and($user->roles)
        ->toHaveCount(2);
});

test('manager role has correct permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'manager')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasPermissionTo('employees.view'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('teams.manage'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('reports.view'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('employees.create'))
        ->toBeFalse();
});

test('people manager role has employee management permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasPermissionTo('employees.view'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('employees.create'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('employees.edit'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('employees.delete'))
        ->toBeTrue();
});

test('owner role has all permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'owner')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasPermissionTo('employees.view'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('organizations.view'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('settings.manage'))
        ->toBeTrue();
});
