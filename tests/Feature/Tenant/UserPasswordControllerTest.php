<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->tenant;
});

it('renders reset password page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-password/create')
                ->has('email')
                ->has('token'));
    });

it('may reset password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Event::fake([PasswordReset::class]);

        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
            ]);

        $token = Password::createToken($user);

        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => $token], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'token' => $token,
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHas('status');

        expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();

        Event::assertDispatched(PasswordReset::class);
    });

it('fails with invalid token',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
            ]);

        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'invalid-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'token' => 'invalid-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'invalid-token']))
            ->assertSessionHasErrors('email');
    });

it('fails with non-existent email',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'nonexistent@example.com',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'token' => 'fake-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']))
            ->assertSessionHasErrors('email');
    });

it('requires email',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'token' => 'fake-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']))
            ->assertSessionHasErrors('email');
    });

it('requires password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'test@example.com',
                'token' => 'fake-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']))
            ->assertSessionHasErrors('password');
    });

it('requires password confirmation',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'new-password',
                'token' => 'fake-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']))
            ->assertSessionHasErrors('password');
    });

it('requires matching password confirmation',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token'], false))
            ->post(tenant_route('tenant.auth.password.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'new-password',
                'password_confirmation' => 'different-password',
                'token' => 'fake-token',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']))
            ->assertSessionHasErrors('password');
    });

it('renders edit password page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->get(tenant_route('tenant.user.settings.password.edit', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('user-password/edit'));
    });

it('may update password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'password' => Hash::make('old-password'),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.password.edit', $this->tenant, [], false))
            ->put(tenant_route('tenant.user.settings.password.update', $this->tenant), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.password.edit', $this->tenant));

        expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
    });

it('requires current password to update',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.password.edit', $this->tenant, [], false))
            ->put(tenant_route('tenant.user.settings.password.update', $this->tenant), [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.password.edit', $this->tenant))
            ->assertSessionHasErrors('current_password');
    });

it('requires correct current password to update',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'password' => Hash::make('old-password'),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.password.edit', $this->tenant, [], false))
            ->put(tenant_route('tenant.user.settings.password.update', $this->tenant), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.password.edit', $this->tenant))
            ->assertSessionHasErrors('current_password');
    });

it('requires new password to update',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'password' => Hash::make('old-password'),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.password.edit', $this->tenant, [], false))
            ->put(tenant_route('tenant.user.settings.password.update', $this->tenant), [
                'current_password' => 'old-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.user.settings.password.edit', $this->tenant))
            ->assertSessionHasErrors('password');
    });

it('redirects authenticated users away from reset password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.auth.password.reset', $this->tenant, ['token' => 'fake-token']));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
    });
