<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

it('renders verify email page', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('home')
        ->get(route('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('user-email-verification-notification/create')
            ->has('status'));
});

it('redirects verified users to dashboard', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('home')
        ->get(route('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier]));

    $response->assertRedirect(route('dashboard', absolute: false));
});

it('may send verification notification', function (): void {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->post(route('tenant.auth.verification.send', ['tenant' => $user->organization->identifier]));

    $response->assertRedirectToRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('redirects verified users when sending notification', function (): void {
    Notification::fake();

    /** @var User $user */
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.auth.verification.notice', ['tenant' => $user->organization->identifier])
        ->post(route('tenant.auth.verification.send', ['tenant' => $user->organization->identifier]));

    $response->assertRedirect(route('dashboard', absolute: false));

    Notification::assertNothingSent();
});
