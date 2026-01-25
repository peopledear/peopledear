<?php

declare(strict_types=1);

use App\Models\CrossDomainAuthToken;
use App\Models\User;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

test('authenticates user with valid token',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        $intended = tenant_route('tenant.org.overview', $this->tenant);

        /** @var CrossDomainAuthToken $token */
        $token = CrossDomainAuthToken::factory()->createQuietly([
            'user_id' => $user->id,
            'intended' => $intended,
        ]);

        $response = $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
            'nonce' => $token->nonce,
        ]));

        $response->assertRedirect($intended);
        $this->assertAuthenticatedAs($user);
    });

test('marks token as used after authentication',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        /** @var CrossDomainAuthToken $token */
        $token = CrossDomainAuthToken::factory()->createQuietly([
            'user_id' => $user->id,
            'intended' => tenant_route('tenant.org.overview', $this->tenant),
        ]);

        expect($token->used_at)->toBeNull();

        $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
            'nonce' => $token->nonce,
        ]));

        expect($token->refresh()->used_at)->not->toBeNull();
    });

test('returns 403 for missing nonce',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant));

        $response->assertForbidden();
        $this->assertGuest();
    });

test('returns 403 for invalid nonce',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
            'nonce' => 'invalid-nonce',
        ]));

        $response->assertForbidden();
        $this->assertGuest();
    });

test('returns 403 for expired token',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        /** @var CrossDomainAuthToken $token */
        $token = CrossDomainAuthToken::factory()->expired()->createQuietly([
            'user_id' => $user->id,
            'intended' => tenant_route('tenant.org.overview', $this->tenant),
        ]);

        $response = $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
            'nonce' => $token->nonce,
        ]));

        $response->assertForbidden();
        $this->assertGuest();
    });

test('returns 403 for already used token',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        /** @var CrossDomainAuthToken $token */
        $token = CrossDomainAuthToken::factory()->used()->createQuietly([
            'user_id' => $user->id,
            'intended' => tenant_route('tenant.org.overview', $this->tenant),
        ]);

        $response = $this->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
            'nonce' => $token->nonce,
        ]));

        $response->assertForbidden();
        $this->assertGuest();
    });

test('redirects authenticated users',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->createQuietly();

        /** @var CrossDomainAuthToken $token */
        $token = CrossDomainAuthToken::factory()->createQuietly([
            'user_id' => $user->id,
            'intended' => tenant_route('tenant.org.overview', $this->tenant),
        ]);

        $response = $this->actingAs($user)
            ->get(tenant_route('tenant.auth.cross-domain', $this->tenant, [
                'nonce' => $token->nonce,
            ]));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
    });
