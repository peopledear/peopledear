# Research: Refactor Notifications to User-Based

**Feature**: 005-refactor-notifications
**Date**: 2025-11-26

## Overview

This research documents the findings and decisions for refactoring the notifications system from Employee-based to User-based, removing organization scoping.

## Research Findings

### 1. Current Implementation Analysis

**Current State:**
- Notifications are stored with `organization_id` (nullable, indexed)
- Model uses `OrganizationScope` and `SetOrganizationScope` via `#[ScopedBy]` attribute
- Notifications are sent to Employee entities (`notifiable_type = App\Models\Employee`)
- Controllers use `#[CurrentEmployee]` attribute for authorization
- `EmployeeNotificationsQuery` queries by employee ID

**Files Affected:**
| File | Current State | Change Required |
|------|---------------|-----------------|
| `app/Models/Notification.php` | Has OrganizationScope, organization relationship | Remove scope and relationship |
| `app/Models/Employee.php` | Has Notifiable and HasNotifications traits | Remove both traits |
| `app/Models/User.php` | Uses HasNotifications to override Notifiable | Use built-in Notifiable only |
| `app/Models/Concerns/HasNotifications.php` | Custom notifications relationship | DELETE |
| `app/Actions/Notifications/MarkAllNotificationsAsRead.php` | Uses Employee | Change to User |
| `app/Http/Controllers/MarkNotificationAsReadController.php` | Uses #[CurrentEmployee] | Change to #[CurrentUser] |
| `app/Http/Controllers/MarkAllNotificationsAsReadController.php` | Uses #[CurrentEmployee] | Change to #[CurrentUser] |
| `app/Http/Controllers/DropdownNotificationController.php` | Uses EmployeeNotificationsQuery | Use UserNotificationsQuery |
| `app/Queries/EmployeeNotificationsQuery.php` | Queries Employee notifications | DELETE |
| `database/factories/NotificationFactory.php` | Has organization_id | Remove organization_id |
| `database/migrations/*_create_notifications_table.php` | Creates org_id column | New migration to remove |

### 2. Migration Strategy

**Decision:** Create a new migration to drop the `organization_id` column

**Rationale:**
- Per project conventions, migrations don't have `down()` methods
- The column is nullable, so no data migration needed
- Existing notification data will be preserved (only org_id is removed)

**Alternatives Considered:**
- Modify existing migration: Rejected - violates immutable migration principle
- Keep column but ignore: Rejected - dead code, confuses future developers

### 3. Authorization Strategy

**Decision:** Use `#[CurrentUser]` attribute and verify `notifiable_id === $user->id`

**Rationale:**
- Simpler than Employee-based authorization
- User is always authenticated, no need for Employee context
- Aligns with Laravel 12 contextual attributes pattern

**Current Authorization Check (in MarkNotificationAsReadController):**
```php
abort_unless($notification->notifiable_id === $employee->id, 403);
```

**New Authorization Check:**
```php
abort_unless($notification->notifiable_id === $user->id, 403);
```

### 4. Query Consolidation

**Decision:** Use existing `UserNotificationsQuery`, delete `EmployeeNotificationsQuery`

**Rationale:**
- `UserNotificationsQuery` already exists and works correctly
- Two parallel queries create confusion and maintenance burden
- Single source of truth for notification queries

### 5. Test Updates

**Decision:** Simplify tests by removing unnecessary Employee/Organization setup

**Current Test Pattern:**
```php
test('user can mark notification as read', function (): void {
    $organization = Organization::factory()->createQuietly();
    $user = User::factory()->createQuietly();
    $employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();
    Session::put('current_organization', $organization->id);
    $employee->notify(...);
    // assertions
});
```

**New Test Pattern:**
```php
test('user can mark notification as read', function (): void {
    $user = User::factory()->createQuietly();
    $user->notify(...);
    // assertions
});
```

### 6. Frontend Impact

**Decision:** No frontend changes required

**Rationale:**
- Frontend already receives notifications via `NotificationListData` DTO
- The data structure (title, message, action_url, read_at, created_at) is unchanged
- Polling endpoint will continue to work with updated backend

## Implementation Order

1. **Create migration** - Drop `organization_id` column
2. **Update Notification model** - Remove OrganizationScope and organization relationship
3. **Update Employee model** - Remove Notifiable and HasNotifications traits
4. **Update User model** - Use built-in Notifiable trait (remove HasNotifications override)
5. **Delete HasNotifications trait** - No longer needed
6. **Update NotificationFactory** - Remove organization_id
7. **Update Actions** - Change MarkAllNotificationsAsRead to use User
8. **Update Controllers** - Change to use #[CurrentUser] and UserNotificationsQuery
9. **Delete EmployeeNotificationsQuery** - No longer needed
10. **Update all tests** - Remove Employee/Organization setup, use User directly

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Orphaned notifications after migration | Low | Low | Notifications retain content; only org_id removed |
| Authorization bypass | Low | High | Tests verify user can only access own notifications |
| Performance regression | Low | Medium | No query changes that would impact performance |

## Decisions Summary

| Topic | Decision | Rationale |
|-------|----------|-----------|
| Migration approach | New migration to drop column | Immutable migrations principle |
| Employee model | Remove Notifiable trait entirely | Employees don't receive notifications |
| User model | Use Laravel's built-in Notifiable | No custom trait needed without organization scoping |
| HasNotifications trait | Delete | Unnecessary without organization scoping |
| Authorization | #[CurrentUser] with direct ID comparison | Simpler, aligns with Laravel 12 |
| Query strategy | Use UserNotificationsQuery only | Single source of truth |
| Test updates | Remove Employee/Organization boilerplate | Tests become simpler and focused |
| Frontend changes | None | Data structure unchanged |
