<?php

declare(strict_types=1);

use App\Models\User;

use function App\tenant_route;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {});

test('renders profile edit page',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(route('dashboard', [], false))
            ->get(tenant_route('tenant.user.settings.profile.edit', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-profile/edit')
                ->has('status'));
    });

test('may update profile information',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
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

test('resets email verification when email changes',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
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

test('keeps email verification when email stays the same',
    /**
     * @throws Throwable
     */
    function (): void {
        $verifiedAt = now();

        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
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

test('requires name',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
                'email' => 'test@example.com',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('name');
    });

test('requires email',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
                'name' => 'Test User',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('email');
    });

test('requires valid email',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
                'name' => 'Test User',
                'email' => 'not-an-email',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('email');
    });

test('requires unique email except own',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $existingUser */
        $existingUser = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly(['email' => 'existing@example.com']);
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly(['email' => 'test@example.com']);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->patch(tenant_route('tenant.user.settings.profile.update', $this->tenant), [
                'name' => 'Test User',
                'email' => 'existing@example.com',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('email');
    });

test('allows keeping same email',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
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
