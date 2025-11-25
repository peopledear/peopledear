<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\PeopleDear\Notification\NotificationListData;
use App\Queries\EmployeeNotificationsQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DropdownNotificationController
{
    public function index(
        Request $request,
        EmployeeNotificationsQuery $employeeNotificationsQuery
    ): Response {

        $notifications = $employeeNotificationsQuery->builder()
            ->get();

        return Inertia::render($request->wantsDropdown() ? 'components/notifications-dropdown/index' : '', [
            'data' => NotificationListData::fromEloquentCollection($notifications),
        ]);
    }
}
