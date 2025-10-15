<?php

declare(strict_types=1);

use App\Models\Role;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;

test('role can be created', function () {
    $role = Role::factory()
        ->create();

    expect($role)->toBeInstanceOf(Role::class)
        ->and($role->id)->toBeInt()
        ->and($role->name)->toBeString()
        ->and($role->display_name)->toBeString()
        ->and($role->created_at)->toBeInstanceOf(CarbonInterface::class)
        ->and($role->updated_at)->toBeInstanceOf(CarbonInterface::class);
});

test('role name must be unique', function () {
    Role::factory()
        ->create(['name' => 'unique-role']);

    expect(fn () => Role::factory()
        ->create(['name' => 'unique-role']))
        ->toThrow(QueryException::class);
});

test('role has users relationship', function () {
    $role = Role::factory()
        ->create();

    expect($role->users())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('to array', function () {
    $role = Role::factory()
        ->create()
        ->refresh();

    expect(array_keys($role->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'name',
            'display_name',
            'description',
        ]);
});

test('base roles exist in database', function () {
    expect(Role::query()
        ->where('name', 'admin')
        ->exists())->toBeTrue()
        ->and(Role::query()
            ->where('name', 'manager')
            ->exists())->toBeTrue()
        ->and(Role::query()
            ->where('name', 'employee')
            ->exists())->toBeTrue();
});

test('base roles have correct attributes', function () {
    $admin = Role::query()
        ->where('name', 'admin')
        ->first();
    expect($admin->display_name)->toBe('Administrator')
        ->and($admin->description)->toBe('Full system access with all permissions');

    $manager = Role::query()
        ->where('name', 'manager')
        ->first();
    expect($manager->display_name)->toBe('Manager')
        ->and($manager->description)->toBe('Can manage team members and approve requests');

    $employee = Role::query()
        ->where('name', 'employee')
        ->first();
    expect($employee->display_name)->toBe('Employee')
        ->and($employee->description)->toBe('Standard employee access');
});
