<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\Employee;

final readonly class MarkAllNotificationsAsRead
{
    public function handle(Employee $employee): void
    {
        $employee->unreadNotifications()
            ->update(['read_at' => now()]);
    }
}
