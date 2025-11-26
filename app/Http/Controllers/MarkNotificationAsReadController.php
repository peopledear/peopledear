<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\MarkNotificationAsRead;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class MarkNotificationAsReadController
{
    public function store(
        Notification $notification,
        MarkNotificationAsRead $action,
        #[CurrentUser] User $user,
    ): RedirectResponse {
        abort_unless($notification->notifiable_id === $user->id, 403);
        $action->handle($notification);

        return back();
    }
}
