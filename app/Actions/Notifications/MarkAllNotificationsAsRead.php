<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;

final readonly class MarkAllNotificationsAsRead
{
    public function handle(User $user): void
    {
        $user->unreadNotifications()
            ->update(['read_at' => now()]);
    }
}
