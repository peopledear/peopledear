<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\CrossDomainAuthToken;
use Illuminate\Database\Eloquent\Builder;

final class CrossDomainAuthTokenQuery
{
    /** @var Builder<CrossDomainAuthToken> */
    private Builder $builder;

    public function __invoke(?string $nonce = null): self
    {
        $this->builder = CrossDomainAuthToken::query();

        if ($nonce) {
            $this->builder->where('nonce', $nonce);
        }

        return $this;
    }

    /**
     * @return Builder<CrossDomainAuthToken>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }

    public function first(): ?CrossDomainAuthToken
    {
        return $this->builder->first();
    }

    public function byNonce(string $nonce): self
    {
        $this->builder->where('nonce', $nonce);

        return $this;
    }
}
