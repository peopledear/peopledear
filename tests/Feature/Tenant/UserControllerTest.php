<?php

declare(strict_types=1);

use App\Models\User;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('deletes user account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'password' => 'password',
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), [
                'password' => 'password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));

        expect($user->fresh())->toBeNull();

        $this->assertGuest();
    });

test('requires password to delete account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), []);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('password');

        expect($user->fresh())->not->toBeNull();
    });

test('requires correct password to delete account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'password' => 'password',
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), [
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('password');

        expect($user->fresh())->not->toBeNull();
    });
