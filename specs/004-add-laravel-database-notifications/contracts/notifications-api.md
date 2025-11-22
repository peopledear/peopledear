# Notifications API Contract

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22

## Endpoints

### GET /notifications

List paginated notifications for authenticated user.

**Request**:
```
GET /notifications?page=1&per_page=15
```

**Query Parameters**:
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page (max 100) |

**Response** (200 OK):
```json
{
  "notifications": [
    {
      "id": "uuid-string",
      "type": "time_off_approved",
      "title": "Time Off Approved",
      "message": "Your time off request has been approved",
      "actionUrl": "/time-off/requests/123",
      "readAt": "2025-11-22T10:30:00Z",
      "createdAt": "2025-11-22T09:00:00Z"
    }
  ],
  "unreadCount": 5,
  "currentPage": 1,
  "lastPage": 3,
  "total": 42
}
```

**Inertia Page**: Returns as page props for `notifications/index.tsx`

---

### POST /notifications/{id}/mark-read

Mark a single notification as read.

**Request**:
```
POST /notifications/{id}/mark-read
```

**Path Parameters**:
| Parameter | Type | Description |
|-----------|------|-------------|
| id | UUID | Notification ID |

**Response** (302 Redirect):
Redirects back with updated notification state.

**Errors**:
- 404: Notification not found
- 403: Notification does not belong to user

---

### POST /notifications/mark-all-read

Mark all notifications as read for authenticated user.

**Request**:
```
POST /notifications/mark-all-read
```

**Response** (302 Redirect):
Redirects back with updated notification state.

---

### DELETE /notifications/{id}

Delete a notification.

**Request**:
```
DELETE /notifications/{id}
```

**Path Parameters**:
| Parameter | Type | Description |
|-----------|------|-------------|
| id | UUID | Notification ID |

**Response** (302 Redirect):
Redirects back after deletion.

**Errors**:
- 404: Notification not found
- 403: Notification does not belong to user

---

## Shared Data

### notificationUnreadCount

Available on every authenticated page via Inertia shared data.

```typescript
interface PageProps {
  notificationUnreadCount: number;
}
```

Access in components:
```typescript
const { notificationUnreadCount } = usePage<PageProps>().props;
```

---

## Routes Summary

| Method | URI | Controller Action | Name |
|--------|-----|-------------------|------|
| GET | /notifications | NotificationController@index | notifications.index |
| POST | /notifications/{id}/mark-read | NotificationController@markAsRead | notifications.mark-read |
| POST | /notifications/mark-all-read | NotificationController@markAllAsRead | notifications.mark-all-read |
| DELETE | /notifications/{id} | NotificationController@destroy | notifications.destroy |

---

## TypeScript Interfaces

```typescript
interface NotificationData {
  id: string;
  type: string;
  title: string;
  message: string;
  actionUrl: string | null;
  readAt: string | null;
  createdAt: string;
}

interface NotificationListData {
  notifications: NotificationData[];
  unreadCount: number;
  currentPage: number;
  lastPage: number;
  total: number;
}
```
