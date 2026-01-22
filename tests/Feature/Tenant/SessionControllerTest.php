<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->organization;
});

it('renders login page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.auth.login', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('session/create')
                ->has('canResetPassword')
                ->has('status'));
    });

it('may create a session',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->withoutTwoFactor()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));

        $this->assertAuthenticatedAs($user);
    });

it('may create a session with remember me',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->withoutTwoFactor()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
                'remember' => true,
            ]);

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));

        $this->assertAuthenticatedAs($user);
    });

it('redirects to two-factor challenge when enabled',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('secret'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.two-factor.login', $this->tenant));

        $this->assertGuest();
    });

it('fails with invalid credentials',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

it('requires email',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'password' => 'password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHasErrors('email');
    });

it('requires password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHasErrors('password');
    });

it('may destroy a session',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(tenant_route('tenant.auth.logout', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));

        $this->assertGuest();
    });

it('redirects authenticated users away from login',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Organization::factory()->create();

        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.auth.login', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
    });

it('throttles login attempts after too many failures',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        for ($i = 0; $i < 5; $i++) {
            $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
                ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                    'email' => 'test@example.com',
                    'password' => 'wrong-password',
                ]);
        }

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHasErrors('email');
    });

it('clears rate limit after successful login',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->withoutTwoFactor()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        for ($i = 0; $i < 3; $i++) {
            $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
                ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                    'email' => 'test@example.com',
                    'password' => 'wrong-password',
                ]);
        }

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
        $this->assertAuthenticatedAs($user);
    });

it('dispatches lockout event when rate limit is reached',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Event::fake();

        $user = User::factory()
            ->withoutTwoFactor()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);

        for ($i = 0; $i < 5; $i++) {
            $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
                ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                    'email' => 'test@example.com',
                    'password' => 'wrong-password',
                ]);
        }

        $response = $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant))
            ->assertSessionHasErrors('email');
    });
