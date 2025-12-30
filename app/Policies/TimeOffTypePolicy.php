<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

final class TimeOffTypePolicy
{
    /**
     * @codeCoverageIgnore
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::TimeOffTypeManage);
    }

    /**
     * @codeCoverageIgnore
     */
    public function view(User $user): bool
    {
        return $user->can(UserPermission::TimeOffTypeView);
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::TimeOffTypeCreate);
    }

    /**
     * @codeCoverageIgnore
     */
    public function update(User $user): bool
    {
        return $user->can(UserPermission::TimeOffTypeEdit);
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete(User $user): bool
    {
        return $user->can(UserPermission::TimeOffTypeDelete);
    }
}
