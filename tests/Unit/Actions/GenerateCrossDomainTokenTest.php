<?php

declare(strict_types=1);

use App\Actions\GenerateCrossDomainToken;
use App\Models\CrossDomainAuthToken;
use App\Models\Organization;
use App\Models\User;

beforeEach(function (): void {
    $this->action = resolve(GenerateCrossDomainToken::class);
});

test('creates token with organization user and intended url', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->for($organization)->createQuietly();

    $token = $this->action->handle($user, $organization, '/dashboard');

    expect($token)
        ->toBeInstanceOf(CrossDomainAuthToken::class)
        ->and($token->organization_id)->toBe($organization->id)
        ->and($token->user_id)->toBe($user->id)
        ->and($token->intended)->toBe('/dashboard')
        ->and($token->used_at)->toBeNull();
});

test('generates unique nonce for each token', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->for($organization)->createQuietly();

    $token1 = $this->action->handle($user, $organization, '/dashboard');
    $token2 = $this->action->handle($user, $organization, '/dashboard');

    expect($token1->nonce)->not->toBe($token2->nonce);
});

test('sets expiration time to 5 minutes from now', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->for($organization)->createQuietly();

    $token = $this->action->handle($user, $organization, '/dashboard');

    expect($token->expires_at->timestamp)
        ->toBe(now()->addMinutes(5)->timestamp);
});

test('persists token to database', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->for($organization)->createQuietly();

    $token = $this->action->handle($user, $organization, '/dashboard');

    $this->assertDatabaseHas('cross_domain_auth_tokens', [
        'id' => $token->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'nonce' => $token->nonce,
        'intended' => '/dashboard',
    ]);
});
