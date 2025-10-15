<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;
use Illuminate\Support\Str;

final class ResendInvitation
{
    public function handle(Invitation $invitation): Invitation
    {
        $invitation->update([
            'token' => Str::random(32),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        return $invitation->fresh() ?? $invitation;
    }
}
