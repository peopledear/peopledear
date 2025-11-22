<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;

final readonly class UserNotificationsQuery
{
    public function __construct(
        #[CurrentUser] private User $user,
    ) {}

    /**
     * Get the query builder for user notifications.
     *
     * @return Builder<Notification>
     */
    public function builder(): Builder
    {
        return Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $this->user->id)
            ->reorder()
            ->orderByRaw('(read_at IS NULL) DESC, created_at DESC');
    }

    /**
     * Get the count of unread notifications.
     */
    public function unreadCount(): int
    {
        return Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $this->user->id)
            ->whereNull('read_at')
            ->count();
    }
}
