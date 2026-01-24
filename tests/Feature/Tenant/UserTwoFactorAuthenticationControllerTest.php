<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function App\tenant_route;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        $this->tenant = $this->organization;
    });

it('renders two factor authentication page',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);

        $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.two-factor.show', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.confirm', $this->tenant), [
                'password' => 'password',
            ]);

        $response = $this->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.user.settings.two-factor.show', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-two-factor-authentication/show')
                ->has('twoFactorEnabled'));
    });

it('shows two factor disabled when not enabled',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->withoutTwoFactor()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);

        $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.two-factor.show', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.confirm', $this->tenant), [
                'password' => 'password',
            ]);

        $response = $this->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.user.settings.two-factor.show', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-two-factor-authentication/show')
                ->has('twoFactorEnabled'));
    });

it('shows two factor enabled when enabled',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('secret'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->actingAs($user)
            ->from(tenant_route('tenant.user.settings.two-factor.show', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.confirm', $this->tenant), [
                'password' => 'password',
            ]);

        $response = $this->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.user.settings.two-factor.show', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-two-factor-authentication/show')
                ->where('twoFactorEnabled', true));
    });
