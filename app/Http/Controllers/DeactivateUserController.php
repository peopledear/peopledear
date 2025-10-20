<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeactivateUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

use function to_route;

final class DeactivateUserController
{
    public function __invoke(
        User $user,
        DeactivateUser $deactivateUser
    ): RedirectResponse {
        $deactivateUser->handle($user);

        return to_route('admin.users.index')
            ->with('success', __('User deactivated successfully'));
    }
}
