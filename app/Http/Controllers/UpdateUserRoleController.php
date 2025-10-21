<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateUserRole;
use App\Data\UpdateUserRoleData;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

use function to_route;

final class UpdateUserRoleController
{
    public function __invoke(
        UpdateUserRoleData $data,
        User $user,
        UpdateUserRole $updateUserRole
    ): RedirectResponse {
        $updateUserRole->handle($user, $data->role_id);

        return to_route('admin.users.index')
            ->with('success', __('User role updated successfully'));
    }
}
