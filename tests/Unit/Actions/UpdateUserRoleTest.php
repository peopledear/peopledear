<?php

declare(strict_types=1);

use App\Actions\UpdateUserRole;
use App\Models\Role;
use App\Models\User;

test('it updates user role_id', function () {
    $oldRole = Role::factory()->create();
    $newRole = Role::factory()->create();
    $user = User::factory()->for($oldRole, 'role')->create();
    $action = new UpdateUserRole();

    expect($user->role_id)->toBe($oldRole->id);

    $result = $action->handle($user, $newRole->id);

    expect($result->role_id)->toBe($newRole->id);
});

test('it returns user with role relationship loaded', function () {
    $newRole = Role::factory()->create();
    $user = User::factory()->create();
    $action = new UpdateUserRole();

    $result = $action->handle($user, $newRole->id);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->role)->toBeInstanceOf(Role::class)
        ->and($result->role->id)->toBe($newRole->id);
});

test('it works with users that have existing roles', function () {
    $oldRole = Role::factory()->create(['name' => 'old-role']);
    $newRole = Role::factory()->create(['name' => 'new-role']);
    $user = User::factory()->for($oldRole, 'role')->create();
    $action = new UpdateUserRole();

    expect($user->role->name)->toBe('old-role');

    $result = $action->handle($user, $newRole->id);

    expect($result->role->name)->toBe('new-role');
});

test('it works with users that have no role', function () {
    $role = Role::factory()->create();
    $user = User::factory()->create(['role_id' => null]);
    $action = new UpdateUserRole();

    expect($user->role_id)->toBeNull();

    $result = $action->handle($user, $role->id);

    expect($result->role_id)->toBe($role->id)
        ->and($result->role)->toBeInstanceOf(Role::class);
});
