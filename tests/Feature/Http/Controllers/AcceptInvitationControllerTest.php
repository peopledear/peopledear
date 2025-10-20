<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('valid invitation token displays registration page', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'invited@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    get(route('invitation.show', $invitation->token))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('AcceptInvitation')
                ->has('invitation')
                ->where('invitation.email', 'invited@example.com')
                ->where('invitation.role', $role->display_name)
                ->where('invitation.token', $invitation->token)
        );
});

test('expired invitation shows 410 error', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'expired@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    get(route('invitation.show', $invitation->token))
        ->assertStatus(410);
});

test('accepted invitation cannot be used again', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'accepted@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => now(),
        'expires_at' => now()->addDays(7),
    ]);

    get(route('invitation.show', $invitation->token))
        ->assertNotFound();
});

test('user can accept invitation and create account', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'name' => 'New User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'))
        ->assertSessionHas('success', 'Welcome to PeopleDear!');

    assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
    ]);

    $user = User::query()->where('email', 'newuser@example.com')->first();
    expect($user->email_verified_at)->not->toBeNull();

    $invitation->refresh();
    expect($invitation->accepted_at)->not->toBeNull();
});

test('user is logged in after accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    post(route('invitation.accept', $invitation->token), [
        'name' => 'New User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    assertAuthenticated();

    $user = User::query()->where('email', 'newuser@example.com')->first();
    expect(auth()->id())->toBe($user->id);
});

test('invalid token shows 404 error', function (): void {
    get(route('invitation.show', 'invalid-token'))
        ->assertNotFound();
});

test('validates required name when accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertInvalid(['name']);
});

test('validates name max length when accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'name' => str_repeat('a', 256),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertInvalid(['name']);
});

test('validates required password when accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'name' => 'New User',
    ]);

    $response->assertInvalid(['password']);
});

test('validates password confirmation matches when accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'name' => 'New User',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertInvalid(['password']);
});

test('cannot accept expired invitation via POST', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'expired@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    $response = post(route('invitation.accept', $invitation->token), [
        'name' => 'New User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('auth.login.index'))
        ->assertSessionHasErrors(['token']);
});

test('cannot accept invitation that does not exist', function (): void {
    $response = post(route('invitation.accept', 'nonexistent-token'), [
        'name' => 'New User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertNotFound();
});

test('user is assigned correct role from invitation', function (): void {
    $managerRole = Role::query()->where('name', 'manager')->first();
    $inviter = User::factory()->admin()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'manager@example.com',
        'role_id' => $managerRole->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    post(route('invitation.accept', $invitation->token), [
        'name' => 'Manager User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::query()->where('email', 'manager@example.com')->first();
    expect($user->role_id)->toBe($managerRole->id)
        ->and($user->isManager())->toBeTrue();
});

test('user email is verified after accepting invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $inviter = User::factory()->create();

    $invitation = Invitation::factory()->create([
        'email' => 'verified@example.com',
        'role_id' => $role->id,
        'invited_by' => $inviter->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    post(route('invitation.accept', $invitation->token), [
        'name' => 'Verified User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::query()->where('email', 'verified@example.com')->first();
    expect($user->email_verified_at)->not->toBeNull();
});
