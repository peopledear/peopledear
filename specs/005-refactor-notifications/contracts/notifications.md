# Notifications API Contracts

**Feature**: 005-refactor-notifications
**Date**: 2025-11-26

## Overview

These endpoints remain unchanged in their interface. The refactor only changes the underlying data model (User instead of Employee, no organization scoping).

## Endpoints

### GET /notifications/dropdown

Fetch notifications for the current user (dropdown view).

**Authentication**: Required
**Response**: Inertia partial response with `NotificationListData`

```typescript
interface NotificationListData {
  notifications: Notification[];
  unread: number;
  total: number;
}

interface Notification {
  id: string;
  type: string;
  title: string;
  message: string;
  actionUrl: string | null;
  readAt: string | null;
  createdAt: string;
  createdAgo: string;
}
```

### POST /notifications/{notification}/mark-read

Mark a single notification as read.

**Authentication**: Required
**Authorization**: User must own the notification
**Response**: Redirect back

### POST /notifications/mark-all-read

Mark all user's notifications as read.

**Authentication**: Required
**Response**: Redirect back

### DELETE /notifications/{notification}

Delete a notification.

**Authentication**: Required
**Authorization**: User must own the notification
**Response**: Redirect back

## Authorization Changes

| Before | After |
|--------|-------|
| `$notification->notifiable_id === $employee->id` | `$notification->notifiable_id === $user->id` |

## No Breaking Changes

- All endpoints maintain the same URLs
- All endpoints maintain the same request/response formats
- Frontend code requires no changes
