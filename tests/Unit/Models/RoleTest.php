<?php

declare(strict_types=1);

use Spatie\Permission\Models\Role;

test('employee role exists in database', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    expect($role)
        ->not->toBeNull()
        ->name->toBe('employee');
});

test('manager role exists in database', function (): void {
    $role = Role::query()
        ->where('name', 'manager')
        ->first();

    expect($role)
        ->not->toBeNull()
        ->name->toBe('manager');
});

test('people manager role exists in database', function (): void {
    $role = Role::query()
        ->where('name', 'people_manager')
        ->first();

    expect($role)
        ->not->toBeNull()
        ->name->toBe('people_manager');
});

test('owner role exists in database', function (): void {
    $role = Role::query()
        ->where('name', 'owner')
        ->first();

    expect($role)
        ->not->toBeNull()
        ->name->toBe('owner');
});

test('all expected roles exist', function (): void {
    $expectedRoles = ['employee', 'manager', 'people_manager', 'owner'];

    $existingRoles = Role::query()
        ->pluck('name')
        ->toArray();

    foreach ($expectedRoles as $roleName) {
        expect($existingRoles)->toContain($roleName);
    }
});
