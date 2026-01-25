<?php

declare(strict_types=1);

use App\Actions\ValidateCrossDomainToken;
use App\Models\CrossDomainAuthToken;
use App\Models\User;

beforeEach(function (): void {
    $this->action = resolve(ValidateCrossDomainToken::class);
});

test('validates token and returns it with user', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly([
        'user_id' => $user->id,
        'intended' => '/dashboard',
    ]);

    $result = $this->action->handle($token->nonce);

    expect($result->id)->toBe($token->id)
        ->and($result->user->id)->toBe($user->id)
        ->and($result->intended)->toBe('/dashboard');
});

test('marks token as used after validation', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    expect($token->used_at)->toBeNull();

    $this->action->handle($token->nonce);

    expect($token->refresh()->used_at)->not->toBeNull();
});

test('throws exception for invalid nonce', function (): void {
    $this->action->handle('invalid-nonce');
})->throws(InvalidArgumentException::class, 'Invalid token');

test('throws exception for expired token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->expired()->createQuietly();

    $this->action->handle($token->nonce);
})->throws(InvalidArgumentException::class, 'Token has expired');

test('throws exception for already used token', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->used()->createQuietly();

    $this->action->handle($token->nonce);
})->throws(InvalidArgumentException::class, 'Token has already been used');

test('prevents replay attacks', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    $this->action->handle($token->nonce);
    $this->action->handle($token->nonce);
})->throws(InvalidArgumentException::class, 'Token has already been used');
