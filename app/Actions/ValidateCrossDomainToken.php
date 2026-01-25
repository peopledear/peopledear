<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CrossDomainAuthToken;
use App\Queries\CrossDomainAuthTokenQuery;
use InvalidArgumentException;

final readonly class ValidateCrossDomainToken
{
    public function __construct(
        private CrossDomainAuthTokenQuery $query,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function handle(string $nonce): CrossDomainAuthToken
    {
        /** @var CrossDomainAuthToken|null $token */
        $token = ($this->query)($nonce)->first();

        throw_if($token === null, InvalidArgumentException::class, 'Invalid token');

        throw_if($token->isExpired(), InvalidArgumentException::class, 'Token has expired');

        throw_if($token->isUsed(), InvalidArgumentException::class, 'Token has already been used');

        $token->markAsUsed();

        return $token;
    }
}
