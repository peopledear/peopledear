<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class ResendInvitation
{
    public function handle(Invitation $invitation): Invitation
    {
        $invitation->update([
            'token' => Str::uuid()->toString(),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)
            ->send(new UserInvitationMail($invitation));

        return $invitation->fresh() ?? $invitation;
    }
}
