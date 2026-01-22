<?php

declare(strict_types=1);

use App\Models\User;

it('renders profile edit page', function (): void {
    /** @var User $user */
    $user = User::factory()
        ->create();

    $response = $this->actingAs($user)
        ->fromRoute('dashboard')
        ->get(route('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier]));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('profile/edit')
            ->has('status'));
});

it('may update profile information', function (): void {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier]);

    expect($user->refresh()->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com');
});

it('resets email verification when email changes', function (): void {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier]);

    expect($user->refresh()->email_verified_at)->toBeNull();
});

it('keeps email verification when email stays the same', function (): void {
    $verifiedAt = now();

    $user = User::factory()->create([
        'email' => 'same@example.com',
        'email_verified_at' => $verifiedAt,
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'New Name',
            'email' => 'same@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier]);

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('requires name', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'email' => 'test@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->assertSessionHasErrors('name');
});

it('requires email', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'Test User',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->assertSessionHasErrors('email');
});

it('requires valid email', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'Test User',
            'email' => 'not-an-email',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->assertSessionHasErrors('email');
});

it('requires unique email except own', function (): void {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->assertSessionHasErrors('email');
});

it('allows keeping same email', function (): void {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response = $this->actingAs($user)
        ->fromRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->patch(route('tenant.user.settings.profile.update', ['tenant' => $user->organization->identifier]), [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
        ]);

    $response->assertRedirectToRoute('tenant.user.settings.profile.edit', ['tenant' => $user->organization->identifier])
        ->assertSessionDoesntHaveErrors();
});
