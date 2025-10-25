<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('user has roles relationship', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    expect($user->roles())->toBeInstanceOf(BelongsToMany::class);
});

test('user has permissions relationship', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    expect($user->permissions())->toBeInstanceOf(BelongsToMany::class);
});

test('user roles relationship is properly loaded', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Role $role */
    $role = Role::query()
        ->where('name', 'manager')
        ->first()
        ?->fresh();

    $user->assignRole($role);
    $user->load('roles');

    expect($user->roles)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->first()
        ->name->toBe('manager');
});

test('user permissions relationship is properly loaded', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    /** @var Permission $permission */
    $permission = Permission::query()
        ->where('name', 'employees.view')
        ->first()
        ?->fresh();

    $user->givePermissionTo($permission);
    $user->load('permissions');

    expect($user->permissions)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->first()
        ->name->toBe('employees.view');
});

test('to array', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'created_at',
            'updated_at',
        ]);
});
