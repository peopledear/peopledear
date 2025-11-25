export interface Notification {
    id: string;
    type: string;
    title: string;
    message: string;
    actionUrl: string | null;
    readAt: string | null;
    createdAt: string;
    createdAgo;
}

export interface NotificationList {
    notifications: Notification[];
    unread: number;
    total: number;
}
