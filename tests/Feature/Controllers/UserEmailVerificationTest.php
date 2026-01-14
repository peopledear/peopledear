<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\URL;

it('may verify email', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
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
        ->fromRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->get($verificationUrl);

    expect($user->refresh()->hasVerifiedEmail())->toBeTrue();

    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

it('redirects to dashboard if already verified', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
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
        ->fromRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->get($verificationUrl);

    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

it('requires valid signature', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $invalidUrl = route('tenant.auth.verification.verify', [
        'tenant' => $user->organization->identifier,
        'id' => $user->getKey(),
        'hash' => sha1((string) $user->email),
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->get($invalidUrl);

    $response->assertForbidden();
});
