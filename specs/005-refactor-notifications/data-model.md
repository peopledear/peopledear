# Data Model: Refactor Notifications to User-Based

**Feature**: 005-refactor-notifications
**Date**: 2025-11-26

## Entity Overview

```
┌──────────────┐         ┌──────────────────┐
│     User     │ 1────∞  │   Notification   │
├──────────────┤         ├──────────────────┤
│ id (PK)      │         │ id (UUID, PK)    │
│ name         │         │ type             │
│ email        │         │ notifiable_type  │
│ ...          │         │ notifiable_id    │◄─── morphs to User
└──────────────┘         │ data (JSON)      │
                         │ read_at          │
                         │ created_at       │
                         │ updated_at       │
                         └──────────────────┘

┌──────────────┐
│   Employee   │  (No longer Notifiable)
├──────────────┤
│ id (PK)      │
│ user_id (FK) │
│ ...          │
└──────────────┘
```

## Notification Entity

### Target Schema (After Refactor)

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | UUID | PRIMARY KEY | Auto-generated |
| type | VARCHAR | NOT NULL | Notification class name |
| notifiable_type | VARCHAR | NOT NULL | `App\Models\User` |
| notifiable_id | BIGINT | NOT NULL | User ID |
| data | TEXT | NOT NULL | JSON-encoded notification content |
| read_at | TIMESTAMP | NULLABLE | When notification was read |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Last update timestamp |

### Removed Column

| Column | Reason |
|--------|--------|
| organization_id | Notifications are user-scoped, not organization-scoped |

### Data Field Structure

```json
{
  "title": "string (required)",
  "message": "string (required)",
  "action_url": "string|null (optional)"
}
```

## Model Changes

### Notification Model

**Remove:**
- `OrganizationScope` from `#[ScopedBy]` attribute
- `SetOrganizationScope` from `#[ScopedBy]` attribute
- `organization()` relationship method
- `organization_id` property annotation

### User Model

**Remove:**
- `HasNotifications` trait import and usage
- The `insteadof` trait conflict resolution

**After:**
```php
use Notifiable;  // Laravel's built-in trait
```

### Employee Model

**Remove:**
- `Notifiable` trait
- `HasNotifications` trait
- The `insteadof` trait conflict resolution
- Related imports

### Files to Delete

| File | Reason |
|------|--------|
| `app/Models/Concerns/HasNotifications.php` | Custom trait no longer needed |
| `app/Queries/EmployeeNotificationsQuery.php` | Employee no longer receives notifications |
| `tests/Unit/Queries/EmployeeNotificationsQueryTest.php` | Query class deleted |
| `tests/Feature/Notifications/EmployeeNotificationsTest.php` | Employee notifications removed |

## Relationships

### Target Relationships (After)

```
Notification morphTo Notifiable (User only)
User morphMany Notifications (via Laravel's Notifiable trait)
Employee has NO notification relationship
```

## State Transitions

```
[Created] ──read_at=null──► [Unread]
                              │
                         markAsRead()
                              │
                              ▼
[Read] ◄──read_at=timestamp──┘
```

## Migration

```php
Schema::table('notifications', function (Blueprint $table): void {
    $table->dropColumn('organization_id');
});
```
