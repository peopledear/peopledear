export interface Notification {
    id: string;
    type: string;
    data: {
        title: string;
        message: string;
        action_utl: string | null;
    };
    read_at: string | null;
    created_at: string;
}

export interface NotificationList {
    notifications: Notification[];
    unreadCount: number;
    currentPage: number;
    lastPage: number;
    total: number;
}
