interface NotificationBadgeProps {
    count: number;
}

export function NotificationBadge({ count }: NotificationBadgeProps) {
    if (count === 0) {
        return null;
    }

    const displayCount = count > 99 ? "99+" : count.toString();

    return (
        <span className="bg-destructive text-destructive-foreground absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full px-1 text-xs font-medium">
            {displayCount}
        </span>
    );
}
