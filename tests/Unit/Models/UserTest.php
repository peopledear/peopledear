<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('user is active by default', function () {
    $user = User::factory()->create();

    expect($user->is_active)->toBeTrue();
});

test('user can be inactive', function () {
    $user = User::factory()->create(['is_active' => false]);

    expect($user->is_active)->toBeFalse();
});

test('user has role relationship', function () {
    $user = User::factory()->create();

    expect($user->role())->toBeInstanceOf(BelongsTo::class);
});

test('user can belong to a role', function () {
    $role = Role::factory()->create();
    $user = User::factory()
        ->for($role)
        ->create();

    expect($user->role_id)->toBe($role->id)
        ->and($user->role->id)->toBe($role->id)
        ->and($user->role->name)->toBe($role->name);
});

test('user has sent invitations relationship', function () {
    $user = User::factory()->create();

    expect($user->sentInvitations())->toBeInstanceOf(HasMany::class);
});

test('user can have sent invitations', function () {
    $user = User::factory()->create();
    $invitation = Invitation::factory()
        ->for($user, 'inviter')
        ->create();

    expect($user->sentInvitations)->toHaveCount(1)
        ->and($user->sentInvitations->first()->id)->toBe($invitation->id);
});

test('isAdmin returns true for admin users', function () {
    $adminRole = Role::query()
        ->where('name', 'admin')
        ->first();
    $user = User::factory()
        ->for($adminRole, 'role')
        ->create();

    expect($user->isAdmin())->toBeTrue();
});

test('isAdmin returns false for non-admin users', function () {
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first();
    $user = User::factory()
        ->for($employeeRole, 'role')
        ->create();

    expect($user->isAdmin())->toBeFalse();
});

test('isAdmin returns false for users without role', function () {
    $user = User::factory()->create(['role_id' => null]);

    expect($user->isAdmin())->toBeFalse();
});

test('isManager returns true for manager users', function () {
    $managerRole = Role::query()
        ->where('name', 'manager')
        ->first();
    $user = User::factory()
        ->for($managerRole, 'role')
        ->create();

    expect($user->isManager())->toBeTrue();
});

test('isManager returns false for non-manager users', function () {
    $adminRole = Role::query()
        ->where('name', 'admin')
        ->first();
    $user = User::factory()
        ->for($adminRole, 'role')
        ->create();

    expect($user->isManager())->toBeFalse();
});

test('isEmployee returns true for employee users', function () {
    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first();
    $user = User::factory()
        ->for($employeeRole, 'role')
        ->create();

    expect($user->isEmployee())->toBeTrue();
});

test('isEmployee returns false for non-employee users', function () {
    $adminRole = Role::query()
        ->where('name', 'admin')
        ->first();
    $user = User::factory()
        ->for($adminRole, 'role')
        ->create();

    expect($user->isEmployee())->toBeFalse();
});

test('hasRole correctly identifies user roles', function () {
    $role = Role::factory()->create(['name' => 'custom-role']);
    $user = User::factory()
        ->for($role, 'role')
        ->create();

    expect($user->hasRole('custom-role'))->toBeTrue()
        ->and($user->hasRole('other-role'))->toBeFalse();
});

test('hasRole returns false when user has no role', function () {
    $user = User::factory()->create(['role_id' => null]);

    expect($user->hasRole('any-role'))->toBeFalse();
});

test('admin factory state creates admin user', function () {
    $user = User::factory()->admin()->create();

    $adminRole = Role::query()
        ->where('name', 'admin')
        ->first();

    expect($user->role_id)->toBe($adminRole->id)
        ->and($user->is_active)->toBeTrue()
        ->and($user->isAdmin())->toBeTrue();
});

test('manager factory state creates manager user', function () {
    $user = User::factory()->manager()->create();

    $managerRole = Role::query()
        ->where('name', 'manager')
        ->first();

    expect($user->role_id)->toBe($managerRole->id)
        ->and($user->is_active)->toBeTrue()
        ->and($user->isManager())->toBeTrue();
});

test('employee factory state creates employee user', function () {
    $user = User::factory()->employee()->create();

    $employeeRole = Role::query()
        ->where('name', 'employee')
        ->first();

    expect($user->role_id)->toBe($employeeRole->id)
        ->and($user->is_active)->toBeTrue()
        ->and($user->isEmployee())->toBeTrue();
});

test('inactive factory state creates inactive user', function () {
    $user = User::factory()->inactive()->create();

    expect($user->is_active)->toBeFalse();
});

test('to array', function () {
    $user = User::factory()
        ->create()
        ->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'avatar',
            'created_at',
            'updated_at',
            'role_id',
            'is_active',
        ]);
});
