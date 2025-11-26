# Quickstart: Refactor Notifications to User-Based

**Feature**: 005-refactor-notifications
**Date**: 2025-11-26

## Overview

This refactor simplifies the notifications system by:
- Removing organization scoping from notifications
- Sending notifications to Users instead of Employees
- Using Laravel's built-in Notifiable trait

## Key Changes

### 1. Database Migration

Create migration to remove `organization_id`:

```bash
php artisan make:migration remove_organization_id_from_notifications --no-interaction
```

### 2. Model Updates

**Notification** - Remove organization scoping:
```php
// Remove #[ScopedBy] attribute entirely
// Remove organization() relationship
```

**User** - Simplify to use built-in Notifiable:
```php
use Notifiable;  // No custom HasNotifications trait
```

**Employee** - Remove notification capability:
```php
// Remove Notifiable trait entirely
// Remove HasNotifications trait
```

### 3. Controller Updates

Replace `#[CurrentEmployee]` with `#[CurrentUser]`:
```php
use Illuminate\Container\Attributes\CurrentUser;

public function store(
    #[CurrentUser] User $user,
    // ...
): RedirectResponse
```

### 4. Action Updates

Update `MarkAllNotificationsAsRead` to use User:
```php
public function handle(User $user): void
{
    $user->unreadNotifications()
        ->update(['read_at' => now()]);
}
```

### 5. Query Updates

Use `UserNotificationsQuery` (delete `EmployeeNotificationsQuery`)

## Testing

Run tests to verify:
```bash
php artisan test --filter=Notification
```

## Files to Delete

- `app/Models/Concerns/HasNotifications.php`
- `app/Queries/EmployeeNotificationsQuery.php`
- `tests/Unit/Queries/EmployeeNotificationsQueryTest.php`
- `tests/Feature/Notifications/EmployeeNotificationsTest.php`
