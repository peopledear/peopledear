<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\DeleteNotification;
use App\Actions\Notifications\MarkAllNotificationsAsRead;
use App\Actions\Notifications\MarkNotificationAsRead;
use App\Data\PeopleDear\Notification\NotificationData;
use App\Data\PeopleDear\Notification\NotificationListData;
use App\Models\Notification;
use App\Models\User;
use App\Queries\UserNotificationsQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class NotificationController
{
    public function index(
        Request $request,
        UserNotificationsQuery $query,
    ): Response {
        $perPageInput = $request->integer('per_page', 15);
        $perPage = min($perPageInput, 100);

        $paginator = $query->builder()->paginate($perPage);

        $notifications = $paginator->map(function (Notification $notification): NotificationData {
            /** @var array{title?: string, message?: string, action_url?: string} $data */
            $data = $notification->data;

            return new NotificationData(
                id: $notification->id,
                type: class_basename($notification->type),
                title: $data['title'] ?? '',
                message: $data['message'] ?? '',
                action_url: $data['action_url'] ?? null,
                read_at: $notification->read_at ? \Illuminate\Support\Facades\Date::parse($notification->read_at) : null,
                created_at: \Illuminate\Support\Facades\Date::parse($notification->created_at),
            );
        })->all();

        return Inertia::render('notifications/index', [
            'notifications' => new NotificationListData(
                notifications: $notifications,
                unread_count: $query->unreadCount(),
                current_page: $paginator->currentPage(),
                last_page: $paginator->lastPage(),
                total: $paginator->total(),
            ),
        ]);
    }

    public function markAsRead(
        Notification $notification,
        MarkNotificationAsRead $action,
        #[CurrentUser] User $user,
    ): RedirectResponse {
        abort_unless($notification->notifiable_id === $user->id, 403);

        $action->handle($notification);

        return back();
    }

    public function markAllAsRead(
        MarkAllNotificationsAsRead $action,
        #[CurrentUser] User $user,
    ): RedirectResponse {
        $action->handle($user);

        return back();
    }

    public function destroy(
        Notification $notification,
        DeleteNotification $action,
        #[CurrentUser] User $user,
    ): RedirectResponse {
        abort_unless($notification->notifiable_id === $user->id, 403);

        $action->handle($notification);

        return back();
    }
}
