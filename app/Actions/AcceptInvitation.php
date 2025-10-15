<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;

final class AcceptInvitation
{
    public function handle(Invitation $invitation): Invitation
    {
        $invitation->update(['accepted_at' => now()]);

        return $invitation->fresh() ?? $invitation;
    }
}
