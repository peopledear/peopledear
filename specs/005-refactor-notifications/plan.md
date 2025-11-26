# Implementation Plan: Refactor Notifications to User-Based

**Branch**: `005-refactor-notifications` | **Date**: 2025-11-26 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/005-refactor-notifications/spec.md`

## Summary

Refactor the notifications system to be User-based instead of Employee-based, removing organization scoping. This simplifies the notification model by:
1. Changing notification ownership from Employee to User
2. Removing `organization_id` column and `OrganizationScope` from notifications
3. Removing Notifiable trait from Employee model (employees don't receive notifications)
4. Using Laravel's built-in Notifiable trait on User (no custom HasNotifications trait needed)
5. Updating all related actions, controllers, queries, and tests

## Technical Context

**Language/Version**: PHP 8.4 with `declare(strict_types=1)`
**Primary Dependencies**: Laravel 12, Spatie Laravel Data v4, Inertia.js v2
**Storage**: PostgreSQL (existing notifications table)
**Testing**: Pest v4 (100% coverage required)
**Target Platform**: Web application (Laravel + React)
**Project Type**: Web application (backend + frontend)
**Performance Goals**: All notification operations < 500ms for up to 100 notifications
**Constraints**: No breaking changes to notification content structure; maintain existing UI behavior
**Scale/Scope**: User-scoped notifications (no multi-tenancy concerns)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Type Safety First | ✅ Pass | All changes will maintain strict typing |
| II. Test Coverage | ✅ Pass | Existing tests will be updated; 100% coverage maintained |
| III. Action Pattern | ✅ Pass | Actions already exist and will be updated (not created new) |
| IV. Laravel Conventions | ✅ Pass | Using Model::query(), Form Requests, contextual attributes |
| V. Simplicity & YAGNI | ✅ Pass | Removing complexity (organization scoping), not adding |

**Pre-Design Gate**: ✅ PASSED

## Project Structure

### Documentation (this feature)

```text
specs/005-refactor-notifications/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks command)
```

### Source Code (repository root)

```text
app/
├── Actions/Notifications/
│   ├── DeleteNotification.php         # No changes needed
│   ├── MarkNotificationAsRead.php     # No changes needed
│   └── MarkAllNotificationsAsRead.php # UPDATE: Change Employee → User
├── Data/PeopleDear/Notification/
│   ├── NotificationData.php           # No changes needed
│   └── NotificationListData.php       # No changes needed
├── Http/Controllers/
│   ├── DeleteNotificationController.php       # No changes needed (already uses #[CurrentUser])
│   ├── DropdownNotificationController.php     # UPDATE: Use UserNotificationsQuery
│   ├── MarkAllNotificationsAsReadController.php # UPDATE: Use #[CurrentUser]
│   └── MarkNotificationAsReadController.php   # UPDATE: Use #[CurrentUser]
├── Models/
│   ├── Notification.php               # UPDATE: Remove OrganizationScope, organization relationship
│   ├── User.php                       # UPDATE: Use built-in Notifiable trait (remove HasNotifications)
│   └── Employee.php                   # UPDATE: Remove Notifiable and HasNotifications traits
├── Models/Concerns/
│   └── HasNotifications.php           # DELETE: No longer needed
├── Queries/
│   ├── UserNotificationsQuery.php     # Keep as primary query
│   └── EmployeeNotificationsQuery.php # DELETE: No longer needed
└── Notifications/
    ├── GeneralNotification.php        # No changes needed
    ├── ActionableNotification.php     # No changes needed
    └── AlertNotification.php          # No changes needed

database/
├── factories/
│   └── NotificationFactory.php        # UPDATE: Remove organization_id
└── migrations/
    └── YYYY_MM_DD_remove_organization_from_notifications.php # CREATE

resources/js/
├── components/notifications/
│   └── dropdown/index.tsx             # No changes needed
└── pages/components/notifications-dropdown/
    └── index.tsx                      # No changes needed

tests/
├── Feature/Controllers/
│   ├── DeleteNotificationControllerTest.php       # UPDATE: Remove Employee/Org setup
│   ├── MarkNotificationAsReadControllerTest.php   # UPDATE: Remove Employee/Org setup
│   └── MarkAllNotificationsAsReadControllerTest.php # UPDATE: Remove Employee/Org setup
├── Feature/Notifications/
│   └── EmployeeNotificationsTest.php              # DELETE: Employee no longer notifiable
├── Unit/Commands/
│   └── PruneOldNotificationsTest.php              # UPDATE: Remove Org scope references
└── Unit/Queries/
    ├── UserNotificationsQueryTest.php             # Keep/verify
    └── EmployeeNotificationsQueryTest.php         # DELETE
```

**Structure Decision**: Existing Laravel web application structure. This refactor simplifies by removing Employee notification support entirely.

## Complexity Tracking

> No violations to justify - this refactor reduces complexity.

| Change | Impact | Justification |
|--------|--------|---------------|
| Remove organization_id | Simplification | Notifications don't need organization scoping per requirements |
| Remove Notifiable from Employee | Simplification | Only Users receive notifications, not Employees |
| Delete HasNotifications trait | Simplification | Use Laravel's built-in Notifiable trait |
| Delete EmployeeNotificationsQuery | Simplification | Replaced by UserNotificationsQuery |
| Delete related test files | Maintenance | Tests for removed functionality |
