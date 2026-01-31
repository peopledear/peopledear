<?php

declare(strict_types=1);

use App\Models\CrossDomainAuthToken;
use App\Models\Organization;
use App\Models\User;

test('belongs to organization', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly([
        'organization_id' => $organization->id,
    ]);

    expect($token->organization->id)->toBe($organization->id);
});

test('belongs to user', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly([
        'user_id' => $user->id,
    ]);

    expect($token->user->id)->toBe($user->id);
});

test('isExpired returns true for expired token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->expired()->createQuietly();

    expect($token->isExpired())->toBeTrue();
});

test('isExpired returns false for valid token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    expect($token->isExpired())->toBeFalse();
});

test('isUsed returns true for used token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->used()->createQuietly();

    expect($token->isUsed())->toBeTrue();
});

test('isUsed returns false for unused token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    expect($token->isUsed())->toBeFalse();
});

test('markAsUsed sets used_at timestamp', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    expect($token->used_at)->toBeNull();

    $token->markAsUsed();

    expect($token->refresh()->used_at)->not->toBeNull();
});

test('prunable returns expired tokens', function (): void {
    /** @var CrossDomainAuthToken $expiredToken */
    $expiredToken = CrossDomainAuthToken::factory()->expired()->createQuietly();

    CrossDomainAuthToken::factory()->createQuietly();

    $prunable = (new CrossDomainAuthToken)->prunable()->get();

    expect($prunable)
        ->toHaveCount(1)
        ->first()->id->toBe($expiredToken->id);
});

test('prunable returns used tokens', function (): void {
    /** @var CrossDomainAuthToken $usedToken */
    $usedToken = CrossDomainAuthToken::factory()->used()->createQuietly();

    CrossDomainAuthToken::factory()->createQuietly();

    $prunable = (new CrossDomainAuthToken)->prunable()->get();

    expect($prunable)
        ->toHaveCount(1)
        ->first()->id->toBe($usedToken->id);
});

test('prunable returns both expired and used tokens', function (): void {
    CrossDomainAuthToken::factory()->expired()->createQuietly();
    CrossDomainAuthToken::factory()->used()->createQuietly();
    CrossDomainAuthToken::factory()->createQuietly();

    $prunable = (new CrossDomainAuthToken)->prunable()->get();

    expect($prunable)->toHaveCount(2);
});

test('to array', function (): void {
    $token = CrossDomainAuthToken::factory()->createQuietly()->refresh();

    expect(array_keys($token->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'user_id',
            'nonce',
            'intended',
            'expires_at',
            'used_at',
        ]);
});
