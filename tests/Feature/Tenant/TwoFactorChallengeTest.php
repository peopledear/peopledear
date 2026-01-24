<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {});

test('renders two factor challenge page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('CCJZI3RPRFVLD2NV'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response = $this->get(tenant_route('tenant.auth.two-factor.login', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-two-factor-authentication-challenge/show'));
    });

test('fails with invalid two factor code',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('CCJZI3RPRFVLD2NV'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response = $this->from(tenant_route('tenant.auth.two-factor.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.two-factor.login.store', $this->tenant), [
                'code' => 'invalid-code',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.two-factor.login', $this->tenant))
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    });

test('fails with invalid recovery code',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('CCJZI3RPRFVLD2NV'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response = $this->from(tenant_route('tenant.auth.two-factor.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.two-factor.login.store', $this->tenant), [
                'recovery_code' => 'invalid-recovery-code',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.two-factor.login', $this->tenant))
            ->assertSessionHasErrors('recovery_code');

        $this->assertGuest();
    });

test('returns validation exception for json request with invalid code',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('CCJZI3RPRFVLD2NV'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response = $this->postJson(tenant_route('tenant.auth.two-factor.login.store', $this->tenant), [
            'code' => 'invalid-code',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    });

test('returns validation exception for json request with invalid recovery code',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly([
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'two_factor_secret' => encrypt('CCJZI3RPRFVLD2NV'),
                'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
                'two_factor_confirmed_at' => now(),
            ]);

        $this->from(tenant_route('tenant.auth.login', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.login.store', $this->tenant), [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $response = $this->postJson(tenant_route('tenant.auth.two-factor.login.store', $this->tenant), [
            'recovery_code' => 'invalid-recovery-code',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('recovery_code');
    });
