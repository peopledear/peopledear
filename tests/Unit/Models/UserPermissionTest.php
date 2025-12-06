<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('user can be assigned a permission directly', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Permission $permission */
    $permission = Permission::query()
        ->where('name', 'employees.view')
        ->first()
        ?->fresh();

    $user->givePermissionTo($permission);

    expect($user->hasPermissionTo('employees.view'))->toBeTrue();
});

test('user inherits permissions from role', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasPermissionTo('profile.address.edit'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('profile.contacts.edit'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('profile.personal.edit'))
        ->toBeTrue();
});

test('user can check if they have any of multiple permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasAnyPermission(['employees.create', 'profile.address.edit']))
        ->toBeTrue()
        ->and($user->hasAnyPermission(['employees.create', 'employees.delete']))
        ->toBeFalse();
});

test('user can check if they have all of multiple permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();

    $user->assignRole($role);

    expect($user->hasAllPermissions(['profile.address.edit', 'profile.contacts.edit']))
        ->toBeTrue()
        ->and($user->hasAllPermissions(['profile.address.edit', 'employees.create']))
        ->toBeFalse();
});

test('direct permissions override role permissions', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'employee')
        ->first()
        ?->fresh();
    /** @var Permission $permission */
    $permission = Permission::query()
        ->where('name', 'employees.create')
        ->first()
        ?->fresh();

    $user->assignRole($role);
    $user->givePermissionTo($permission);

    expect($user->hasPermissionTo('employees.create'))
        ->toBeTrue()
        ->and($user->hasPermissionTo('profile.address.edit'))
        ->toBeTrue();
});
