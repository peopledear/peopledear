<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class UpdateUserRole
{
    public function handle(User $user, int $roleId): User
    {
        $user->update(['role_id' => $roleId]);

        return $user->fresh(['role']) ?? $user;
    }
}
