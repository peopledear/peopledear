<?php

declare(strict_types=1);

use Spatie\Permission\Models\Permission;

test('employees view permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'employees.view')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('employees.view');
});

test('employees create permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'employees.create')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('employees.create');
});

test('employees edit permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'employees.edit')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('employees.edit');
});

test('employees delete permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'employees.delete')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('employees.delete');
});

test('organizations view permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'organizations.view')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('organizations.view');
});

test('profile address edit permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'profile.address.edit')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('profile.address.edit');
});

test('profile contacts edit permission exists in database', function (): void {
    $permission = Permission::query()
        ->where('name', 'profile.contacts.edit')
        ->first();

    expect($permission)
        ->not->toBeNull()
        ->name->toBe('profile.contacts.edit');
});

test('all expected permissions exist', function (): void {
    $expectedPermissions = [
        'employees.view',
        'employees.create',
        'employees.edit',
        'employees.delete',
        'organizations.view',
        'organizations.create',
        'organizations.edit',
        'organizations.delete',
        'profile.address.edit',
        'profile.contacts.edit',
        'profile.personal.edit',
        'teams.manage',
        'reports.view',
        'settings.manage',
    ];

    $existingPermissions = Permission::query()
        ->pluck('name')
        ->toArray();

    foreach ($expectedPermissions as $permissionName) {
        expect($existingPermissions)->toContain($permissionName);
    }
});
