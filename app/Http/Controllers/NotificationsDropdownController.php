<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Queries\EmployeeNotificationsQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

final class NotificationsDropdownController
{
    public function index(
        Request $request,
        EmployeeNotificationsQuery $employeeNotificationsQuery,
        #[CurrentUser] User $user
    ): Response {

        return Inertia::render($request->wantsDropdown() ? 'components/notifications-dropdown/index' : '', [
            'notifications' => Cache::remember(
                key: 'notifications.dropdown.'.$user->id,
                ttl: now()->addMinutes(5),
                callback: fn () => $employeeNotificationsQuery->builder()
                    ->take(5)
                    ->get()
            ),
        ]);
    }
}
