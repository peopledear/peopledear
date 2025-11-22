<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\Notification;

final readonly class DeleteNotification
{
    public function handle(Notification $notification): void
    {
        $notification->delete();
    }
}
