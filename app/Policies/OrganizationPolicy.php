<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

final class OrganizationPolicy
{
    /**
     * @codeCoverageIgnore
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::OrganizationManage);
    }

    public function view(User $user): bool
    {

        return $user->can(UserPermission::OrganizationView);
    }

    public function update(User $user): bool
    {
        return $user->can(UserPermission::OrganizationEdit);
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete(User $user): bool
    {
        return $user->can(UserPermission::OrganizationDelete);
    }
}
