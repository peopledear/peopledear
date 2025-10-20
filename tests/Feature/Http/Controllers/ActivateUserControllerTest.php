<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
});

test('admin can activate user', function (): void {
    $user = User::factory()->inactive()->create();

    $response = actingAs($this->admin)
        ->post(route('admin.users.activate', $user));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User activated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});

test('user status changes to active', function (): void {
    $user = User::factory()->inactive()->create();

    expect($user->is_active)->toBeFalse();

    actingAs($this->admin)
        ->post(route('admin.users.activate', $user));

    $user->refresh();

    expect($user->is_active)->toBeTrue();
});

test('activating already active user succeeds', function (): void {
    $user = User::factory()->create(['is_active' => true]);

    $response = actingAs($this->admin)
        ->post(route('admin.users.activate', $user));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User activated successfully');

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => true,
    ]);
});

test('non-admin cannot activate user', function (): void {
    $employee = User::factory()->employee()->create();
    $user = User::factory()->inactive()->create();

    $response = actingAs($employee)
        ->post(route('admin.users.activate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

test('manager cannot activate user', function (): void {
    $manager = User::factory()->manager()->create();
    $user = User::factory()->inactive()->create();

    $response = actingAs($manager)
        ->post(route('admin.users.activate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

test('requires authentication to activate user', function (): void {
    $user = User::factory()->inactive()->create();

    $response = post(route('admin.users.activate', $user));

    $response->assertRedirect(route('auth.login.index'));

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});

test('handles activating non-existent user', function (): void {
    $response = actingAs($this->admin)
        ->post(route('admin.users.activate', 99999));

    $response->assertNotFound();
});

test('admin can activate another admin', function (): void {
    $inactiveAdmin = User::factory()->admin()->inactive()->create();

    $response = actingAs($this->admin)
        ->post(route('admin.users.activate', $inactiveAdmin));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User activated successfully');

    assertDatabaseHas('users', [
        'id' => $inactiveAdmin->id,
        'is_active' => true,
    ]);
});

test('inactive admin cannot activate user', function (): void {
    $inactiveAdmin = User::factory()->admin()->inactive()->create();
    $user = User::factory()->inactive()->create();

    $response = actingAs($inactiveAdmin)
        ->post(route('admin.users.activate', $user));

    $response->assertForbidden();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'is_active' => false,
    ]);
});
