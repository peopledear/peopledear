<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\NotificationData;
use App\Models\Notification;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

final class ShareNotificationCount
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $notifications = $request->user()
                ->notifications()
                ->take(5)
                ->get()
                ->map(function (Notification $notification): NotificationData {
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
                })
                ->all();

            Inertia::share('notificationUnreadCount', $request->user()->unreadNotifications()->count());
            Inertia::share('recentNotifications', $notifications);
        }

        return $next($request);
    }
}
