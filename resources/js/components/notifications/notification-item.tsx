import { Button } from "@/components/ui/button";
import type { NotificationData } from "@/types/notifications";
import { router } from "@inertiajs/react";
import { Check, Trash2 } from "lucide-react";

interface NotificationItemProps {
    notification: NotificationData;
}

export function NotificationItem({ notification }: NotificationItemProps) {
    const isUnread = !notification.readAt;

    const handleMarkAsRead = () => {
        router.post(
            `/notifications/${notification.id}/mark-read`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const handleDelete = () => {
        router.delete(`/notifications/${notification.id}`, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleClick = () => {
        if (isUnread) {
            handleMarkAsRead();
        }
        if (notification.actionUrl) {
            router.visit(notification.actionUrl);
        }
    };

    const formattedDate = new Date(notification.createdAt).toLocaleDateString(
        undefined,
        {
            month: "short",
            day: "numeric",
            hour: "numeric",
            minute: "2-digit",
        },
    );

    return (
        <div
            className={`hover:bg-muted/50 cursor-pointer border-b p-4 transition-colors ${
                isUnread ? "bg-primary/5" : ""
            }`}
            onClick={handleClick}
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2">
                        <span
                            className={`truncate font-medium ${isUnread ? "text-foreground" : "text-muted-foreground"}`}
                        >
                            {notification.title}
                        </span>
                        {isUnread && (
                            <span className="bg-primary h-2 w-2 flex-shrink-0 rounded-full" />
                        )}
                    </div>
                    <p className="text-muted-foreground mt-1 line-clamp-2 text-sm">
                        {notification.message}
                    </p>
                    <p className="text-muted-foreground mt-2 text-xs">
                        {formattedDate}
                    </p>
                </div>
                <div className="flex flex-shrink-0 items-center gap-1">
                    {isUnread && (
                        <Button
                            variant="ghost"
                            size="icon"
                            className="h-8 w-8"
                            onClick={(e) => {
                                e.stopPropagation();
                                handleMarkAsRead();
                            }}
                            title="Mark as read"
                        >
                            <Check className="h-4 w-4" />
                        </Button>
                    )}
                    <Button
                        variant="ghost"
                        size="icon"
                        className="text-destructive hover:text-destructive h-8 w-8"
                        onClick={(e) => {
                            e.stopPropagation();
                            handleDelete();
                        }}
                        title="Delete notification"
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    );
}
