export interface NotificationData {
    id: string;
    type: string;
    title: string;
    message: string;
    actionUrl: string | null;
    readAt: string | null;
    createdAt: string;
}

export interface NotificationListData {
    notifications: NotificationData[];
    unreadCount: number;
    currentPage: number;
    lastPage: number;
    total: number;
}
