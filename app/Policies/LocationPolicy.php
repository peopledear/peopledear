<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

final class LocationPolicy
{
    /**
     * @codeCoverageIgnore
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::LocationManage);
    }

    /**
     * @codeCoverageIgnore
     */
    public function view(User $user): bool
    {
        return $user->can(UserPermission::LocationManage);
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::LocationCreate);
    }

    public function update(User $user): bool
    {
        return $user->can(UserPermission::LocationEdit);
    }

    public function delete(User $user): bool
    {
        return $user->can(UserPermission::LocationsDelete);
    }
}
