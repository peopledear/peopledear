<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Builder;

final class PendingInvitationsQuery
{
    /**
     * @return Builder<Invitation>
     */
    public function builder(): Builder
    {
        return Invitation::query()
            ->with(['role', 'inviter'])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())->latest();
    }
}
