# Data Model: Laravel Database Notifications

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22

## Entities

### Notification

Laravel's built-in notification entity stored in the `notifications` table.

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | UUID | PK | Unique notification identifier |
| type | string | NOT NULL | Fully qualified notification class name |
| notifiable_type | string | NOT NULL, INDEX | Polymorphic type (e.g., "App\Models\User") |
| notifiable_id | bigint | NOT NULL, INDEX | User ID (foreign key) |
| data | JSON | NOT NULL | Notification payload (title, message, action_url, etc.) |
| read_at | timestamp | NULLABLE | When notification was marked as read |
| created_at | timestamp | NOT NULL | When notification was created |
| updated_at | timestamp | NOT NULL | When notification was last updated |

**Indexes**:
- `notifications_notifiable_type_notifiable_id_index` (composite)

**Relationships**:
- Belongs to User (via polymorphic notifiable)

### User (Extended)

Uses `Notifiable` trait providing notification methods.

**New Relationships**:
- Has many Notifications (via `notifications()`)
- Has many Unread Notifications (via `unreadNotifications()`)
- Has many Read Notifications (via `readNotifications()`)

## Data Objects

### NotificationData

Transfer object for notification list items.

```php
final class NotificationData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $title,
        public readonly string $message,
        public readonly ?string $actionUrl,
        public readonly ?Carbon $readAt,
        public readonly Carbon $createdAt,
    ) {}
}
```

### NotificationListData

Paginated collection of notifications.

```php
final class NotificationListData extends Data
{
    public function __construct(
        /** @var array<NotificationData> */
        public readonly array $notifications,
        public readonly int $unreadCount,
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly int $total,
    ) {}
}
```

## Notification Types

The system will support different notification types via distinct notification classes:

| Type | Class | Use Case |
|------|-------|----------|
| Informational | `GeneralNotification` | System announcements, reminders |
| Actionable | `ActionableNotification` | Requires user action (includes action_url) |
| Alert | `AlertNotification` | Important warnings or alerts |

## State Transitions

### Notification Lifecycle

```
[Created] → [Unread] → [Read] → [Deleted]
                ↓
           [Pruned after 90 days]
```

**Transitions**:
- Created → Unread: Automatic on notification creation
- Unread → Read: User marks notification as read (sets `read_at`)
- Read/Unread → Deleted: User explicitly deletes notification
- Any → Pruned: Automatic cleanup after 90 days

## Validation Rules

### Mark as Read
- Notification must exist
- Notification must belong to authenticated user
- Notification must not already be deleted

### Delete Notification
- Notification must exist
- Notification must belong to authenticated user

### List Notifications
- User must be authenticated
- Page number must be positive integer
- Per page must be between 1 and 100
