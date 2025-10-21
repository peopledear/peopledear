<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patch;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
});

test('admin can update user role', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $user = User::factory()->create(['role_id' => $employeeRole->id]);

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $managerRole->id,
        ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User role updated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $managerRole->id,
    ]);
});

test('user role changes correctly', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $user = User::factory()->create(['role_id' => $employeeRole->id]);

    expect($user->role_id)->toBe($employeeRole->id)
        ->and($user->isEmployee())->toBeTrue();

    actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $managerRole->id,
        ]);

    $user->refresh();

    expect($user->role_id)->toBe($managerRole->id)
        ->and($user->isManager())->toBeTrue();
});

test('admin can update employee to manager', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $user = User::factory()->create(['role_id' => $employeeRole->id]);

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $managerRole->id,
        ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User role updated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $managerRole->id,
    ]);
});

test('admin can update manager to admin', function (): void {
    $managerRole = Role::query()->where('name', 'manager')->first();
    $adminRole = Role::query()->where('name', 'admin')->first();

    $user = User::factory()->create(['role_id' => $managerRole->id]);

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $adminRole->id,
        ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User role updated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $adminRole->id,
    ]);
});

test('admin can downgrade admin to employee', function (): void {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $employeeRole = Role::query()->where('name', 'employee')->first();

    $user = User::factory()->create(['role_id' => $adminRole->id]);

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $employeeRole->id,
        ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User role updated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $employeeRole->id,
    ]);
});

test('validates required role_id', function (): void {
    $user = User::factory()->employee()->create();

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), []);

    $response->assertInvalid(['role_id']);
});

test('validates role_id exists', function (): void {
    $user = User::factory()->employee()->create();

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => 99999,
        ]);

    $response->assertInvalid(['role_id']);
});

test('non-admin cannot update user role', function (): void {
    $employee = User::factory()->employee()->create();
    $managerRole = Role::query()->where('name', 'manager')->first();
    $user = User::factory()->employee()->create();

    $originalRoleId = $user->role_id;

    $response = actingAs($employee)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $managerRole->id,
        ]);

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $originalRoleId,
    ]);
});

test('manager cannot update user role', function (): void {
    $manager = User::factory()->manager()->create();
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->employee()->create();

    $originalRoleId = $user->role_id;

    $response = actingAs($manager)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $adminRole->id,
        ]);

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $originalRoleId,
    ]);
});

test('requires authentication to update user role', function (): void {
    $user = User::factory()->employee()->create();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $originalRoleId = $user->role_id;

    $response = patch(route('admin.users.role.update', $user), [
        'role_id' => $managerRole->id,
    ]);

    $response->assertRedirect(route('auth.login.index'));

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $originalRoleId,
    ]);
});

test('handles updating role for non-existent user', function (): void {
    $managerRole = Role::query()->where('name', 'manager')->first();

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', 99999), [
            'role_id' => $managerRole->id,
        ]);

    $response->assertNotFound();
});

test('admin can update their own role', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();

    $response = actingAs($this->admin)
        ->patch(route('admin.users.role.update', $this->admin), [
            'role_id' => $employeeRole->id,
        ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User role updated successfully');

    assertDatabaseHas('users', [
        'id' => $this->admin->id,
        'role_id' => $employeeRole->id,
    ]);
});

test('inactive admin cannot update user role', function (): void {
    $inactiveAdmin = User::factory()->admin()->inactive()->create();
    $user = User::factory()->employee()->create();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $originalRoleId = $user->role_id;

    $response = actingAs($inactiveAdmin)
        ->patch(route('admin.users.role.update', $user), [
            'role_id' => $managerRole->id,
        ]);

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'role_id' => $originalRoleId,
    ]);
});
