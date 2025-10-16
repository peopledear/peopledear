<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;

beforeEach(function (): void {
    $this->admin = User::factory()
        ->create(['role_id' => Role::query()->where('name', 'admin')->first()->id]);

    $this->actingAs($this->admin);
});

test('it creates an invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('invitations.store'), [
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('users.index'))
        ->assertSessionHas('success', __('Invitation sent successfully'));

    $invitation = Invitation::query()
        ->where('email', 'newuser@example.com')
        ->first();

    expect($invitation)->not->toBeNull()
        ->and($invitation->email)->toBe('newuser@example.com')
        ->and($invitation->role_id)->toBe($role->id)
        ->and($invitation->invited_by)->toBe($this->admin->id)
        ->and($invitation->token)->not->toBeNull()
        ->and($invitation->expires_at)->not->toBeNull()
        ->and($invitation->accepted_at)->toBeNull();
});

test('it validates required email when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('invitations.store'), [
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('it validates email format when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('invitations.store'), [
        'email' => 'invalid-email',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('it validates email max length when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('invitations.store'), [
        'email' => str_repeat('a', 256).'@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('it validates required role_id when creating invitation', function (): void {
    $response = $this->post(route('invitations.store'), [
        'email' => 'test@example.com',
    ]);

    $response->assertInvalid(['role_id']);
});

test('it validates role_id exists when creating invitation', function (): void {
    $response = $this->post(route('invitations.store'), [
        'email' => 'test@example.com',
        'role_id' => 99999,
    ]);

    $response->assertInvalid(['role_id']);
});

test('it deletes an invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $invitation = Invitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
    ]);

    $response = $this->delete(route('invitations.destroy', $invitation));

    $response->assertRedirect(route('users.index'))
        ->assertSessionHas('success', __('Invitation deleted successfully'));

    expect(Invitation::query()->find($invitation->id))->toBeNull();
});

test('it requires authentication to create invitation', function (): void {
    auth()->logout();

    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('invitations.store'), [
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('auth.login.index'));
});

test('it requires authentication to delete invitation', function (): void {
    auth()->logout();

    $invitation = Invitation::factory()->create();

    $response = $this->delete(route('invitations.destroy', $invitation));

    $response->assertRedirect(route('auth.login.index'));
});

test('it handles deleting non-existent invitation', function (): void {
    $response = $this->delete(route('invitations.destroy', 99999));

    $response->assertNotFound();
});
