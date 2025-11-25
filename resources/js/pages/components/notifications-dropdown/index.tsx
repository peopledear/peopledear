import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { ButtonGroup } from "@/components/ui/button-group";
import {
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemFooter,
    ItemGroup,
    ItemTitle,
} from "@/components/ui/item";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import { Notification, NotificationList } from "@/types/notifications";
import DeleteNotificationController from "@/wayfinder/actions/App/Http/Controllers/DeleteNotificationController";
import MarkAllNotificationsAsReadController from "@/wayfinder/actions/App/Http/Controllers/MarkAllNotificationsAsReadController";
import MarkNotificationAsReadController from "@/wayfinder/actions/App/Http/Controllers/MarkNotificationAsReadController";
import { router } from "@inertiajs/react";
import { CheckCircle2, Trash2 } from "lucide-react";
import { Fragment, useEffect, useState } from "react";

export default function NotificationsDropdownComponent({
    notifications: notificationsProp,
    unread: unreadProp,
}: NotificationList) {
    const [notifications, setNotifications] =
        useState<Notification[]>(notificationsProp);
    const [unread, setUnread] = useState<number>(unreadProp);

    // Sync state when props change (from polling)
    useEffect(() => {
        setNotifications(notificationsProp);
        setUnread(unreadProp);
    }, [notificationsProp, unreadProp]);

    const markAsRead = (id: string) => {
        router.post(
            MarkNotificationAsReadController.store(id),
            {},
            {
                onSuccess: () => {
                    setNotifications((prev) =>
                        prev.map((notification) => {
                            if (notification.id === id) {
                                if (notification.readAt === null) {
                                    setUnread((u) => Math.max(0, u - 1));
                                }
                                return {
                                    ...notification,
                                    readAt: new Date().toISOString(),
                                };
                            }
                            return notification;
                        }),
                    );
                },
            },
        );
    };

    const markAllAsRead = () => {
        router.post(
            MarkAllNotificationsAsReadController.store(),
            {},
            {
                onSuccess: () => {
                    setNotifications((prev) =>
                        prev.map((notification) => ({
                            ...notification,
                            readAt: new Date().toISOString(),
                        })),
                    );
                    setUnread(0);
                },
            },
        );
    };

    const deleteNotification = (id: string) => {
        router.delete(DeleteNotificationController.destroy(id), {
            onSuccess: () => {
                setNotifications((prev) => {
                    const found = prev.find(
                        (notification) => notification.id === id,
                    );
                    if (!found) return prev;

                    if (found.readAt === null) {
                        setUnread((u) => Math.max(0, u - 1));
                    }
                    return prev.filter(
                        (notification) => notification.id !== id,
                    );
                });
            },
        });
    };

    return (
        <>
            <DropdownMenuContent
                className="max-h-[686px] w-[386px] min-w-64 overflow-y-auto sm:w-[416px] sm:-translate-x-9"
                align="start"
            >
                <DropdownMenuLabel>
                    <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <Button variant="ghost">
                                <span>Unread</span>
                                <Badge
                                    variant="secondary"
                                    className="border border-gray-200"
                                >
                                    {unread}
                                </Badge>
                            </Button>
                        </div>
                        <Button variant="link" onClick={() => markAllAsRead()}>
                            Mark all as read
                        </Button>
                    </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <ItemGroup>
                    {notifications &&
                        notifications.map((notification) => (
                            <Fragment key={notification.id}>
                                <Item className="group relative transition-all hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <ItemContent>
                                        <ItemTitle className="flex w-full items-center justify-between gap-2">
                                            <span>{notification.title}</span>
                                            {notification.readAt === null && (
                                                <span
                                                    className="flex size-2.5 rounded-full bg-green-600"
                                                    title="Unread"
                                                />
                                            )}
                                        </ItemTitle>
                                        <ItemDescription>
                                            <span
                                                className="line-clamp-4 overflow-hidden text-sm text-wrap"
                                                dangerouslySetInnerHTML={{
                                                    __html: notification.message,
                                                }}
                                            />
                                        </ItemDescription>
                                    </ItemContent>
                                    <ItemActions className="absolute top-4 right-2 hidden transition-all duration-300 group-hover:block">
                                        <ButtonGroup>
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() =>
                                                            deleteNotification(
                                                                notification.id,
                                                            )
                                                        }
                                                    >
                                                        <Trash2 className="text-muted-foreground size-5" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    Delete Notification
                                                </TooltipContent>
                                            </Tooltip>
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() =>
                                                            markAsRead(
                                                                notification.id,
                                                            )
                                                        }
                                                    >
                                                        <CheckCircle2 className="text-muted-foreground size-5" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    Mark as Read
                                                </TooltipContent>
                                            </Tooltip>
                                        </ButtonGroup>
                                    </ItemActions>
                                    <ItemFooter>
                                        <span className="text-muted-foreground">
                                            {notification.createdAgo}
                                        </span>
                                    </ItemFooter>
                                </Item>
                            </Fragment>
                        ))}
                </ItemGroup>
            </DropdownMenuContent>
        </>
    );
}
