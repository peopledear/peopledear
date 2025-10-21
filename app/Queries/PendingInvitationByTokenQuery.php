<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Builder;

final class PendingInvitationByTokenQuery
{
    private string $token;

    private bool $withRelations = false;

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function withRole(): self
    {
        $this->withRelations = true;

        return $this;
    }

    /**
     * @return Builder<Invitation>
     */
    public function builder(): Builder
    {
        $query = Invitation::query()
            ->where('token', $this->token)
            ->whereNull('accepted_at');

        if ($this->withRelations) {
            $query->with('role');
        }

        return $query;
    }
}
