<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\TimeOffRequest;
use App\Models\User;

final class TimeOffRequestPolicy
{
    /**
     * @codeCoverageIgnore
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::TimeOffRequestManage);
    }

    /**
     * @codeCoverageIgnore
     */
    public function view(User $user, TimeOffRequest $timeOffRequest): bool
    {
        return $user->can(UserPermission::TimeOffRequestView)
            && $user->id === $timeOffRequest->employee->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::TimeOffRequestCreate);
    }

    /**
     * @codeCoverageIgnore
     */
    public function update(User $user, TimeOffRequest $timeOffRequest): bool
    {
        if ($user->can(UserPermission::TimeOffRequestManage)) {
            return true;
        }

        return $user->can(UserPermission::TimeOffRequestEdit)
            && $user->id === $timeOffRequest->employee->user_id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete(User $user, TimeOffRequest $timeOffRequest): bool
    {
        if ($user->can(UserPermission::TimeOffRequestManage)) {
            return true;
        }

        return $user->can(UserPermission::TimeOffRequestDelete)
            && $user->id === $timeOffRequest->employee->user_id;
    }
}
