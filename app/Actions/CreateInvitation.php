<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class CreateInvitation
{
    public function handle(string $email, int $roleId, int $invitedBy): Invitation
    {
        $invitation = Invitation::query()
            ->create([
                'email' => $email,
                'role_id' => $roleId,
                'invited_by' => $invitedBy,
                'token' => Str::uuid()->toString(),
                'expires_at' => now()->addDays(7),
            ]);

        Mail::to($invitation->email)
            ->send(new UserInvitationMail($invitation));

        return $invitation;
    }
}
