<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class DeactivateUser
{
    public function handle(User $user): User
    {
        $user->update(['is_active' => false]);

        return $user->fresh() ?? $user;
    }
}
