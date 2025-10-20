<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
});

test('admin can deactivate user', function (): void {
    $user = User::factory()->create(['is_active' => true]);

    $response = actingAs($this->admin)
        ->post(route('admin.users.deactivate', $user));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deactivated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

test('user status changes to inactive', function (): void {
    $user = User::factory()->create(['is_active' => true]);

    expect($user->is_active)->toBeTrue();

    actingAs($this->admin)
        ->post(route('admin.users.deactivate', $user));

    $user->refresh();

    expect($user->is_active)->toBeFalse();
});

test('deactivating already inactive user succeeds', function (): void {
    $user = User::factory()->inactive()->create();

    $response = actingAs($this->admin)
        ->post(route('admin.users.deactivate', $user));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deactivated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

test('non-admin cannot deactivate user', function (): void {
    $employee = User::factory()->employee()->create();
    $user = User::factory()->create(['is_active' => true]);

    $response = actingAs($employee)
        ->post(route('admin.users.deactivate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});

test('manager cannot deactivate user', function (): void {
    $manager = User::factory()->manager()->create();
    $user = User::factory()->create(['is_active' => true]);

    $response = actingAs($manager)
        ->post(route('admin.users.deactivate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});

test('requires authentication to deactivate user', function (): void {
    $user = User::factory()->create(['is_active' => true]);

    $response = post(route('admin.users.deactivate', $user));

    $response->assertRedirect(route('auth.login.index'));

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});

test('handles deactivating non-existent user', function (): void {
    $response = actingAs($this->admin)
        ->post(route('admin.users.deactivate', 99999));

    $response->assertNotFound();
});

test('admin can deactivate another admin', function (): void {
    $activeAdmin = User::factory()->admin()->create(['is_active' => true]);

    $response = actingAs($this->admin)
        ->post(route('admin.users.deactivate', $activeAdmin));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deactivated successfully');

    assertDatabaseHas('users', [
        'id' => $activeAdmin->id,
        'is_active' => false,
    ]);
});

test('admin can deactivate themselves', function (): void {
    $response = actingAs($this->admin)
        ->post(route('admin.users.deactivate', $this->admin));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deactivated successfully');

    assertDatabaseHas('users', [
        'id' => $this->admin->id,
        'is_active' => false,
    ]);
});

test('inactive admin cannot deactivate user', function (): void {
    $inactiveAdmin = User::factory()->admin()->inactive()->create();
    $user = User::factory()->create(['is_active' => true]);

    $response = actingAs($inactiveAdmin)
        ->post(route('admin.users.deactivate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});
