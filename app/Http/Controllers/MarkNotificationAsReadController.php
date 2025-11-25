<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\MarkNotificationAsRead;
use App\Attributes\CurrentEmployee;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;

final class MarkNotificationAsReadController
{
    public function store(
        Notification $notification,
        MarkNotificationAsRead $action,
        #[CurrentEmployee] Employee $employee,
    ): RedirectResponse {
        abort_unless($notification->notifiable_id === $employee->id, 403);
        $action->handle($notification);

        return back();
    }
}
