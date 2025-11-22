# Research: Laravel Database Notifications

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22

## 1. Database Migration Setup

**Decision**: Use Laravel's built-in `php artisan make:notifications-table` command

**Rationale**:
- Battle-tested migration template with proper indexing
- `read_at` nullable timestamp is industry standard for unread tracking
- UUID primary keys align with modern Laravel practices

**Alternatives considered**:
- Custom migration from scratch - rejected; Laravel's template includes proper indexing
- Adding custom columns immediately - rejected; keep migrations focused per guidelines

## 2. Notification Data Objects

**Decision**: Create Data objects in `app/Data/` with Spatie Laravel Data v4

**Rationale**:
- Automatic JSON serialization for database storage
- Type-safe DTOs prevent data corruption
- Aligns with existing PeopleDear Data conventions

**Alternatives considered**:
- Raw arrays in database - rejected for lack of type safety
- Custom serializers - rejected; Data v4 handles serialization automatically

## 3. Efficient Querying

**Decision**: Separate queries for unread count and paginated list

**Rationale**:
- `$user->unreadNotifications()->count()` returns single DB query result
- Avoid loading entire collection into memory (critical for 1000+ notifications)
- Order by `read_at` then `created_at DESC` for UX (unread first)

**Alternatives considered**:
- Load all then filter in PHP - rejected due to memory issues
- Complex single query with subqueries - rejected for clarity

## 4. Notification Pruning (90-day retention)

**Decision**: Use Laravel's `MassPrunable` trait with scheduled `model:prune`

**Rationale**:
- Efficient mass delete operations
- Built-in scheduler integration
- Configurable via `prunable()` method

**Alternatives considered**:
- Custom artisan command - rejected; `model:prune` is cleaner
- Delete on read - rejected; read notifications have archival value

## 5. Frontend Notification Pattern

**Decision**: Inertia shared data for count + polling on dropdown interaction

**Rationale**:
- Shared data includes unread count on every response
- Polling only triggers when user opens dropdown
- Simplicity over WebSocket complexity for HR SaaS workflows

**Alternatives considered**:
- Continuous polling - rejected; wastes bandwidth
- WebSockets - rejected; complexity not justified for HR notification frequency
- Observer pattern - rejected; Inertia shared data is already reactive

## Implementation Patterns

### Query Class Pattern

```php
final readonly class UserNotificationsQuery {
    public function builder(User $user): Builder {
        return $user->notifications()
            ->orderBy('read_at', 'asc')      // Unread first
            ->orderBy('created_at', 'desc'); // Newest within group
    }

    public function unreadCount(User $user): int {
        return $user->unreadNotifications()->count();
    }
}
```

### Prunable Model Pattern

```php
final class Notification extends Model {
    use MassPrunable;

    public function prunable(): Builder {
        return static::where('created_at', '<=', now()->subDays(90));
    }
}
```

### Shared Data Middleware Pattern

```php
// App/Http/Middleware/ShareNotificationCount.php
class ShareNotificationCount {
    public function handle(Request $request, Closure $next): Response {
        if ($request->user()) {
            Inertia::share('notificationUnreadCount',
                $request->user()->unreadNotifications()->count()
            );
        }
        return $next($request);
    }
}
```

## Summary

| Topic | Decision | Trade-off |
|-------|----------|-----------|
| DB Migration | Built-in command | Simplicity over customization |
| Data Objects | Spatie Data v4 | Type safety over raw arrays |
| Pagination | Separate queries | Two queries vs one complex |
| Pruning | MassPrunable | Automated vs manual cleanup |
| Frontend | Polling + shared data | Simplicity over real-time |
