import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import type { SharedData } from "@/types";
import type { Notification } from "@/types/notifications";
import { router, usePage } from "@inertiajs/react";
import { Bell, CheckCheck } from "lucide-react";
import { NotificationBadge } from "./notification-badge";
import { NotificationItem } from "./notification-item";

interface PageProps extends SharedData {
    notificationUnreadCount?: number;
    recentNotifications?: Notification[];
}

export function NotificationDropdown() {
    const { notificationUnreadCount, recentNotifications } =
        usePage<PageProps>().props;
    const unreadCount = notificationUnreadCount ?? 0;
    const notifications = recentNotifications ?? [];

    const handleMarkAllAsRead = () => {
        router.post(
            "/notifications/mark-all-read",
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const handleViewAll = () => {
        router.visit("/notifications");
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="relative">
                    <Bell className="size-5" />
                    <NotificationBadge count={unreadCount} />
                    <span className="sr-only">Notifications</span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-96 p-0">
                <div className="flex items-center justify-between border-b p-4">
                    <h3 className="font-semibold">Notifications</h3>
                    {unreadCount > 0 && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={handleMarkAllAsRead}
                            className="h-8 text-xs"
                        >
                            <CheckCheck className="mr-1 h-4 w-4" />
                            Mark all read
                        </Button>
                    )}
                </div>
                <div className="max-h-96 overflow-y-auto">
                    {notifications.length === 0 ? (
                        <div className="text-muted-foreground p-8 text-center">
                            <Bell className="mx-auto mb-2 h-8 w-8 opacity-50" />
                            <p>No notifications</p>
                        </div>
                    ) : (
                        notifications.map((notification) => (
                            <NotificationItem
                                key={notification.id}
                                notification={notification}
                            />
                        ))
                    )}
                </div>
                {notifications.length > 0 && (
                    <div className="border-t p-2">
                        <Button
                            variant="ghost"
                            className="w-full"
                            onClick={handleViewAll}
                        >
                            View all notifications
                        </Button>
                    </div>
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
