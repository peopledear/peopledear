<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

final class OfficePolicy
{
    /**
     * @codeCoverageIgnore
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::OfficeManage);
    }

    /**
     * @codeCoverageIgnore
     */
    public function view(User $user): bool
    {
        return $user->can(UserPermission::OfficeManage);
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::OfficeCreate);
    }

    public function update(User $user): bool
    {
        return $user->can(UserPermission::OfficeEdit);
    }

    public function delete(User $user): bool
    {
        return $user->can(UserPermission::OfficeDelete);
    }
}
