<?php

declare(strict_types=1);

use App\Models\CrossDomainAuthToken;
use App\Queries\CrossDomainAuthTokenQuery;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function (): void {
    $this->query = new CrossDomainAuthTokenQuery;
});

test('returns eloquent builder instance', function (): void {
    $builder = ($this->query)()->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

test('builder returns cross domain auth token query builder', function (): void {
    $builder = ($this->query)()->builder();

    expect($builder->getModel())->toBeInstanceOf(CrossDomainAuthToken::class);
});

test('invoke with nonce filters by nonce', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    /** @var CrossDomainAuthToken $otherToken */
    $otherToken = CrossDomainAuthToken::factory()->createQuietly();

    $found = ($this->query)($token->nonce)->first();

    expect($found)
        ->not->toBeNull()
        ->id->toBe($token->id);
});

test('invoke without nonce returns all tokens', function (): void {
    /** @var CrossDomainAuthToken $token1 */
    $token1 = CrossDomainAuthToken::factory()->createQuietly();

    /** @var CrossDomainAuthToken $token2 */
    $token2 = CrossDomainAuthToken::factory()->createQuietly();

    $tokens = ($this->query)()->builder()->get();

    expect($tokens)->toHaveCount(2);
});

test('byNonce filters by nonce', function (): void {
    /** @var CrossDomainAuthToken $token */
    $token = CrossDomainAuthToken::factory()->createQuietly();

    $found = ($this->query)()->byNonce($token->nonce)->first();

    expect($found)
        ->not->toBeNull()
        ->id->toBe($token->id);
});

test('first returns null when nonce does not exist', function (): void {
    $found = ($this->query)('non-existent-nonce')->first();

    expect($found)->toBeNull();
});
