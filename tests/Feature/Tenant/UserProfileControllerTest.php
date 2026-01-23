<?php

declare(strict_types=1);

use App\Models\User;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->organization;
});

it('renders profile edit page', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create();

    $response = $this->actingAs($user)
        ->from(route('dashboard', [], false))
        ->get(tenant_route('tenant.user.settings.profile.edit', $this->tenant));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('user-profile/edit')
            ->has('status'));
});

it('may update profile information', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant));

    expect($user->refresh()->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com');
});

it('resets email verification when email changes', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant));

    expect($user->refresh()->email_verified_at)->toBeNull();
});

it('keeps email verification when email stays the same', function (): void {
    $verifiedAt = now();

    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'email' => 'same@example.com',
            'email_verified_at' => $verifiedAt,
        ]);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'New Name',
            'email' => 'same@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('requires name', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create();

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'email' => 'test@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
        ->assertSessionHasErrors('name');
});

it('requires email', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create();

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'Test User',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
        ->assertSessionHasErrors('email');
});

it('requires valid email', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create();

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'Test User',
            'email' => 'not-an-email',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
        ->assertSessionHasErrors('email');
});

it('requires unique email except own', function (): void {
    $existingUser = User::factory()
        ->for($this->tenant, 'organization')
        ->create(['email' => 'existing@example.com']);
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create(['email' => 'test@example.com']);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
        ->assertSessionHasErrors('email');
});

it('allows keeping same email', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
        ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
        ]);

    $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
        ->assertSessionDoesntHaveErrors();
});
