<?php

declare(strict_types=1);

namespace App\Queries;

use App\Attributes\CurrentEmployee;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final readonly class EmployeeNotificationsQuery
{
    public function __construct(
        #[CurrentEmployee] private Employee $employee,
    ) {}

    /**
     * Get the query builder for user notifications.
     *
     * @return Builder<Notification>
     */
    public function builder(): Builder
    {

        return Notification::query()
            ->where('notifiable_type', Employee::class)
            ->where('notifiable_id', $this->employee->id)
            ->reorder()
            ->orderByRaw('(read_at IS NULL) DESC, created_at DESC');
    }

    /**
     * Get the count of unread notifications.
     */
    public function unreadCount(): int
    {
        return Notification::query()
            ->where('notifiable_type', Employee::class)
            ->where('notifiable_id', $this->employee->id)
            ->whereNull('read_at')
            ->count();
    }
}
