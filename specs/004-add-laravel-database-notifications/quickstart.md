# Quickstart: Laravel Database Notifications

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22

## Prerequisites

- Laravel 12 application with Inertia.js + React
- PostgreSQL database
- User model with `Notifiable` trait (already present)

## Quick Setup

### 1. Create Notifications Table

```bash
php artisan make:notifications-table
php artisan migrate
```

### 2. Create Notification Model (for pruning)

```bash
php artisan make:model Notification
```

Add `MassPrunable` trait with 90-day retention.

### 3. Create Actions

```bash
php artisan make:action "Notifications/MarkNotificationAsRead" --no-interaction
php artisan make:action "Notifications/MarkAllNotificationsAsRead" --no-interaction
php artisan make:action "Notifications/DeleteNotification" --no-interaction
```

### 4. Create Query

```bash
php artisan make:class "Queries/UserNotificationsQuery" --no-interaction
```

### 5. Create Controller

```bash
php artisan make:controller NotificationController --no-interaction
```

### 6. Add Routes

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});
```

### 7. Create Middleware for Shared Data

```bash
php artisan make:middleware ShareNotificationCount --no-interaction
```

Register in `bootstrap/app.php` web middleware.

### 8. Schedule Pruning

Add to `bootstrap/app.php`:

```php
Schedule::command('model:prune', [
    '--model' => [App\Models\Notification::class],
])->daily();
```

### 9. Create Frontend Components

- `resources/js/components/notifications/notification-dropdown.tsx`
- `resources/js/components/notifications/notification-item.tsx`
- `resources/js/components/notifications/notification-badge.tsx`

## Testing

```bash
# Run all notification tests
php artisan test --filter=Notification

# Verify pruning (dry run)
php artisan model:prune --pretend
```

## Sending Test Notification

```php
// In tinker or test
$user = User::first();
$user->notify(new \App\Notifications\TestNotification());
```

## Verification Checklist

- [ ] Notifications table exists in database
- [ ] Notification badge shows unread count
- [ ] Dropdown displays notifications
- [ ] Mark as read works
- [ ] Mark all as read works
- [ ] Delete notification works
- [ ] Pruning scheduled in console
- [ ] All tests pass with 100% coverage
