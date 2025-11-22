<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\Notification;

final readonly class MarkNotificationAsRead
{
    public function handle(Notification $notification): void
    {
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }
    }
}
