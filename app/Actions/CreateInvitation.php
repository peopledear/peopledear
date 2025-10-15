<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;
use Illuminate\Support\Str;

final class CreateInvitation
{
    public function handle(string $email, int $roleId, int $invitedBy): Invitation
    {
        return Invitation::query()
            ->create([
                'email' => $email,
                'role_id' => $roleId,
                'invited_by' => $invitedBy,
                'token' => Str::random(32),
                'expires_at' => now()->addDays(7),
                'accepted_at' => null,
            ]);
    }
}
