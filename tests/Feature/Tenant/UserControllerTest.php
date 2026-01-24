<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->tenant;
});

it('may delete user account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'password' => Hash::make('password'),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), [
                'password' => 'password',
            ]);

        $response->assertRedirectToRoute('home');

        expect($user->fresh())->toBeNull();

        $this->assertGuest();
    });

it('requires password to delete account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), []);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('password');

        expect($user->fresh())->not->toBeNull();
    });

it('requires correct password to delete account',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'password' => Hash::make('password'),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.profile.edit', $this->tenant, [], false))
            ->delete(tenant_route('tenant.user.destroy', $this->tenant), [
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.profile.edit', $this->tenant))
            ->assertSessionHasErrors('password');

        expect($user->fresh())->not->toBeNull();
    });
