<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\URL;

use function App\tenant_route;

beforeEach(function (): void {});

it('may verify email', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'email_verified_at' => null,
        ]);

    $verificationUrl = URL::temporarySignedRoute(
        'tenant.auth.verification.verify',
        now()->addMinutes(60),
        [
            'tenant' => $user->organization->identifier,
            'id' => $user->getKey(),
            'hash' => sha1((string) $user->email),
        ]
    );

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.auth.verification.notice', $this->tenant, [], false))
        ->get($verificationUrl);

    expect($user->refresh()->hasVerifiedEmail())->toBeTrue();

    $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant).'?verified=1');
});

it('redirects to dashboard if already verified', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'email_verified_at' => now(),
        ]);

    $verificationUrl = URL::temporarySignedRoute(
        'tenant.auth.verification.verify',
        now()->addMinutes(60),
        [
            'tenant' => $user->organization->identifier,
            'id' => $user->getKey(),
            'hash' => sha1((string) $user->email),
        ]
    );

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.auth.verification.notice', $this->tenant, [], false))
        ->get($verificationUrl);

    $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant).'?verified=1');
});

it('requires valid signature', function (): void {
    $user = User::factory()
        ->for($this->tenant, 'organization')
        ->create([
            'email_verified_at' => null,
        ]);

    $invalidUrl = tenant_route('tenant.auth.verification.verify', $this->tenant, [
        'id' => $user->getKey(),
        'hash' => sha1((string) $user->email),
    ]);

    $response = $this->actingAs($user)
        ->from(tenant_route('tenant.auth.verification.notice', $this->tenant, [], false))
        ->get($invalidUrl);

    $response->assertForbidden();
});
