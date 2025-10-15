<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class ActivateUser
{
    public function handle(User $user): User
    {
        $user->update(['is_active' => true]);

        return $user->fresh() ?? $user;
    }
}
