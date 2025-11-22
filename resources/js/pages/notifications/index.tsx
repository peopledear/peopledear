import { NotificationItem } from "@/components/notifications/notification-item";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import AppLayout from "@/layouts/app-layout";
import type { NotificationListData } from "@/types/notifications";
import { Head, router } from "@inertiajs/react";
import { Bell, CheckCheck } from "lucide-react";

interface NotificationsIndexProps {
    notifications: NotificationListData;
}

export default function NotificationsIndex({
    notifications,
}: NotificationsIndexProps) {
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

    const handleLoadMore = () => {
        if (notifications.currentPage < notifications.lastPage) {
            router.get(
                "/notifications",
                {
                    page: notifications.currentPage + 1,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        }
    };

    return (
        <AppLayout>
            <Head title="Notifications" />

            <div className="mx-auto max-w-3xl px-4 py-8">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle className="flex items-center gap-2">
                            <Bell className="h-5 w-5" />
                            Notifications
                            {notifications.unreadCount > 0 && (
                                <span className="text-muted-foreground text-sm font-normal">
                                    ({notifications.unreadCount} unread)
                                </span>
                            )}
                        </CardTitle>
                        {notifications.unreadCount > 0 && (
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={handleMarkAllAsRead}
                            >
                                <CheckCheck className="mr-2 h-4 w-4" />
                                Mark all as read
                            </Button>
                        )}
                    </CardHeader>
                    <CardContent className="p-0">
                        {notifications.notifications.length === 0 ? (
                            <div className="text-muted-foreground p-12 text-center">
                                <Bell className="mx-auto mb-4 h-12 w-12 opacity-50" />
                                <p className="text-lg font-medium">
                                    No notifications
                                </p>
                                <p className="mt-1 text-sm">
                                    You're all caught up! New notifications will
                                    appear here.
                                </p>
                            </div>
                        ) : (
                            <>
                                <div className="divide-y">
                                    {notifications.notifications.map(
                                        (notification) => (
                                            <NotificationItem
                                                key={notification.id}
                                                notification={notification}
                                            />
                                        ),
                                    )}
                                </div>
                                {notifications.currentPage <
                                    notifications.lastPage && (
                                    <div className="border-t p-4 text-center">
                                        <Button
                                            variant="outline"
                                            onClick={handleLoadMore}
                                        >
                                            Load more
                                        </Button>
                                    </div>
                                )}
                            </>
                        )}
                    </CardContent>
                </Card>

                {notifications.total > 0 && (
                    <p className="text-muted-foreground mt-4 text-center text-sm">
                        Showing {notifications.notifications.length} of{" "}
                        {notifications.total} notifications
                    </p>
                )}
            </div>
        </AppLayout>
    );
}
