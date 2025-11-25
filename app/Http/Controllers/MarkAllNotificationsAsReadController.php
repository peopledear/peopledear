<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\MarkAllNotificationsAsRead;
use App\Attributes\CurrentEmployee;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;

final class MarkAllNotificationsAsReadController
{
    public function store(
        MarkAllNotificationsAsRead $action,
        #[CurrentEmployee] Employee $employee,
    ): RedirectResponse {
        $action->handle($employee);

        return back();
    }
}
