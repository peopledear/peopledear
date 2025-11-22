# Tasks: Laravel Database Notifications

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22

## Implementation Strategy

- **MVP**: User Story 1 (View Unread Notifications) - provides core value
- **Incremental delivery**: Each user story is independently testable and deployable
- **Constitution compliance**: 100% test coverage, Action pattern, type safety throughout

---

## Phase 1: Setup

Database and infrastructure setup required for all user stories.

- [ ] T001 Generate notifications table migration via `php artisan make:notifications-table`
- [ ] T002 Run migration to create notifications table
- [ ] T003 Create Notification model with MassPrunable trait in `app/Models/Notification.php`
- [ ] T004 [P] Create NotificationData DTO in `app/Data/NotificationData.php`
- [ ] T005 [P] Create NotificationListData DTO in `app/Data/NotificationListData.php`
- [ ] T006 Create UserNotificationsQuery in `app/Queries/UserNotificationsQuery.php`
- [ ] T007 Create ShareNotificationCount middleware in `app/Http/Middleware/ShareNotificationCount.php`
- [ ] T008 Register ShareNotificationCount middleware in `bootstrap/app.php`
- [ ] T009 Add notification routes to `routes/web.php`

---

## Phase 2: Foundational

Shared test infrastructure and notification types needed by all user stories.

- [ ] T010 Create test helper for notification factory in `tests/Pest.php` or test setup
- [ ] T011 [P] Create GeneralNotification class in `app/Notifications/GeneralNotification.php`
- [ ] T012 [P] Create ActionableNotification class in `app/Notifications/ActionableNotification.php`
- [ ] T013 [P] Create AlertNotification class in `app/Notifications/AlertNotification.php`

---

## Phase 3: User Story 1 - View Unread Notifications (P1)

**Goal**: Users can see their unread notifications in a header dropdown with relevant details.

**Independent Test**: Trigger notification for user → verify they see it in list with message, timestamp, type.

**Acceptance Criteria**:
- Notifications display with message, timestamp, read/unread indicator
- Empty state shown when no notifications
- Notifications ordered newest first
- Pagination works for large lists

### Tests

- [ ] T014 [US1] Write ViewNotificationsTest for listing notifications in `tests/Feature/Notifications/ViewNotificationsTest.php`
- [ ] T015 [US1] Write UserNotificationsQueryTest in `tests/Unit/Queries/UserNotificationsQueryTest.php`

### Implementation

- [ ] T016 [US1] Create NotificationController with index action in `app/Http/Controllers/NotificationController.php`
- [ ] T017 [US1] Add TypeScript interfaces in `resources/js/types/notifications.d.ts`
- [ ] T018 [US1] Create notification-item component in `resources/js/components/notifications/notification-item.tsx`
- [ ] T019 [US1] Create notification-dropdown component in `resources/js/components/notifications/notification-dropdown.tsx`
- [ ] T020 [US1] Integrate notification dropdown into app layout header

---

## Phase 4: User Story 2 - Mark Notifications as Read (P2)

**Goal**: Users can mark individual or all notifications as read.

**Independent Test**: Mark notification as read → verify status changes and unread count decreases.

**Acceptance Criteria**:
- Individual notification can be marked as read
- All notifications can be marked as read at once
- State persists after page refresh
- Idempotent operation (marking read notification as read = no error)

### Tests

- [ ] T021 [US2] Write MarkNotificationAsReadTest in `tests/Feature/Notifications/MarkNotificationAsReadTest.php`
- [ ] T022 [US2] Write MarkAllNotificationsAsReadTest in `tests/Feature/Notifications/MarkAllNotificationsAsReadTest.php`

### Implementation

- [ ] T023 [US2] Create MarkNotificationAsRead action in `app/Actions/Notifications/MarkNotificationAsRead.php`
- [ ] T024 [US2] Create MarkAllNotificationsAsRead action in `app/Actions/Notifications/MarkAllNotificationsAsRead.php`
- [ ] T025 [US2] Add markAsRead method to NotificationController in `app/Http/Controllers/NotificationController.php`
- [ ] T026 [US2] Add markAllAsRead method to NotificationController in `app/Http/Controllers/NotificationController.php`
- [ ] T027 [US2] Add mark as read button to notification-item component in `resources/js/components/notifications/notification-item.tsx`
- [ ] T028 [US2] Add mark all as read button to notification-dropdown in `resources/js/components/notifications/notification-dropdown.tsx`

---

## Phase 5: User Story 3 - Notification Count Badge (P2)

**Goal**: Users see unread notification count badge on the bell icon.

**Independent Test**: Create notifications → verify badge shows correct count → mark as read → verify count updates.

**Acceptance Criteria**:
- Badge displays unread count
- Badge updates when count changes
- No badge (or zero) when no unread notifications

### Tests

- [ ] T029 [US3] Write NotificationBadgeTest for count display in `tests/Feature/Notifications/NotificationBadgeTest.php`

### Implementation

- [ ] T030 [US3] Create notification-badge component in `resources/js/components/notifications/notification-badge.tsx`
- [ ] T031 [US3] Integrate badge into notification-dropdown component in `resources/js/components/notifications/notification-dropdown.tsx`
- [ ] T032 [US3] Verify shared data middleware provides count on all authenticated pages

---

## Phase 6: User Story 4 - Delete Notifications (P3)

**Goal**: Users can delete notifications to keep their list clean.

**Independent Test**: Delete notification → verify it no longer appears in list.

**Acceptance Criteria**:
- Individual notifications can be deleted
- Deletion persists after page refresh
- Proper authorization (only owner can delete)

### Tests

- [ ] T033 [US4] Write DeleteNotificationTest in `tests/Feature/Notifications/DeleteNotificationTest.php`

### Implementation

- [ ] T034 [US4] Create DeleteNotification action in `app/Actions/Notifications/DeleteNotification.php`
- [ ] T035 [US4] Add destroy method to NotificationController in `app/Http/Controllers/NotificationController.php`
- [ ] T036 [US4] Add delete button to notification-item component in `resources/js/components/notifications/notification-item.tsx`

---

## Phase 7: Polish & Cross-Cutting Concerns

Final cleanup, pruning setup, and authorization.

- [ ] T037 Schedule notification pruning (90 days) in `bootstrap/app.php`
- [ ] T038 Write PruneOldNotificationsTest in `tests/Feature/Notifications/PruneOldNotificationsTest.php`
- [ ] T039 Add authorization checks to all controller actions (user can only access own notifications)
- [ ] T040 Run `composer test:unit` and verify 100% coverage
- [ ] T041 Run `vendor/bin/pint --dirty` to format code
- [ ] T042 Run `composer test:types` for static analysis
- [ ] T043 Manual browser testing of complete notification flow

---

## Dependencies

```
Phase 1 (Setup) → Phase 2 (Foundational) → Phase 3 (US1) → Phase 4 (US2)
                                                        ↘ Phase 5 (US3)
                                         Phase 3 (US1) → Phase 6 (US4)

All User Stories → Phase 7 (Polish)
```

**Notes**:
- US2 depends on US1 (need to view notifications before marking them)
- US3 depends on US1 (badge count relates to viewed notifications)
- US4 depends on US1 (need to view notifications before deleting them)
- US2 and US3 can be worked in parallel after US1

---

## Parallel Execution Opportunities

### Within Setup (Phase 1)
- T004, T005 (DTOs) can run in parallel
- T007, T008 (middleware) sequential due to dependency

### Within Foundational (Phase 2)
- T011, T012, T013 (notification types) can all run in parallel

### After US1 Complete
- US2 (Mark as Read) and US3 (Badge) can start in parallel
- US4 (Delete) can start after US1

### Within Each User Story
- Tests (T014-T015, T021-T022, etc.) should run before implementation
- Frontend components can be parallelized with backend actions when interfaces are defined

---

## Summary

| Phase | Description | Task Count |
|-------|-------------|------------|
| Phase 1 | Setup | 9 |
| Phase 2 | Foundational | 4 |
| Phase 3 | US1 - View Notifications | 7 |
| Phase 4 | US2 - Mark as Read | 8 |
| Phase 5 | US3 - Badge Count | 4 |
| Phase 6 | US4 - Delete | 4 |
| Phase 7 | Polish | 7 |
| **Total** | | **43** |

**MVP Scope**: Phases 1-3 (US1) = 20 tasks
**Full Implementation**: All phases = 43 tasks

**Parallel Opportunities**: 12 tasks marked with [P]
