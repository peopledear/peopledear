<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\MarkAllNotificationsAsRead;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class MarkAllNotificationsAsReadController
{
    public function store(
        MarkAllNotificationsAsRead $action,
        #[CurrentUser] User $user,
    ): RedirectResponse {
        $action->handle($user);

        return back();
    }
}
