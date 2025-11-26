# Tasks: Refactor Notifications to User-Based

**Input**: Design documents from `/specs/005-refactor-notifications/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: Tests are REQUIRED per constitution (100% coverage). Test tasks included for updates.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Backend**: `app/`, `database/`, `tests/`
- **Frontend**: `resources/js/` (no changes needed per plan)

---

## Phase 1: Setup

**Purpose**: No setup needed - this is a refactor of existing functionality

*All infrastructure already exists. Proceed to Phase 2.*

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core data model changes that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [ ] T001 Create migration to drop organization_id column in `database/migrations/YYYY_MM_DD_HHMMSS_remove_organization_id_from_notifications.php`
- [ ] T002 Update Notification model - remove OrganizationScope, SetOrganizationScope, and organization relationship in `app/Models/Notification.php`
- [ ] T003 [P] Update Employee model - remove Notifiable and HasNotifications traits in `app/Models/Employee.php`
- [ ] T004 [P] Update User model - use built-in Notifiable trait, remove HasNotifications override in `app/Models/User.php`
- [ ] T005 [P] Delete HasNotifications trait file `app/Models/Concerns/HasNotifications.php`
- [ ] T006 [P] Update NotificationFactory - remove organization_id in `database/factories/NotificationFactory.php`
- [ ] T007 [P] Delete EmployeeNotificationsQuery file `app/Queries/EmployeeNotificationsQuery.php`
- [ ] T008 [P] Delete EmployeeNotificationsQueryTest file `tests/Unit/Queries/EmployeeNotificationsQueryTest.php`
- [ ] T009 [P] Delete EmployeeNotificationsTest file `tests/Feature/Notifications/EmployeeNotificationsTest.php`
- [ ] T010 Run migration `php artisan migrate`

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - View Personal Notifications (Priority: P1) 🎯 MVP

**Goal**: Users can view their notifications in the dropdown, sorted by unread first then creation date

**Independent Test**: Login as user, trigger notification via `$user->notify()`, verify it appears in dropdown

### Implementation for User Story 1

- [ ] T011 [US1] Update DropdownNotificationController to use UserNotificationsQuery in `app/Http/Controllers/DropdownNotificationController.php`
- [ ] T012 [US1] Verify UserNotificationsQuery works correctly (no changes needed) in `app/Queries/UserNotificationsQuery.php`

### Tests for User Story 1

- [ ] T013 [US1] Verify UserNotificationsQueryTest passes in `tests/Unit/Queries/UserNotificationsQueryTest.php`

**Checkpoint**: User Story 1 complete - users can view their notifications

---

## Phase 4: User Story 2 - Mark Notifications as Read (Priority: P2)

**Goal**: Users can mark individual or all notifications as read

**Independent Test**: Create unread notifications for user, mark as read via UI/API, verify read status persists

### Implementation for User Story 2

- [ ] T014 [US2] Update MarkAllNotificationsAsRead action to use User instead of Employee in `app/Actions/Notifications/MarkAllNotificationsAsRead.php`
- [ ] T015 [P] [US2] Update MarkNotificationAsReadController to use #[CurrentUser] in `app/Http/Controllers/MarkNotificationAsReadController.php`
- [ ] T016 [P] [US2] Update MarkAllNotificationsAsReadController to use #[CurrentUser] in `app/Http/Controllers/MarkAllNotificationsAsReadController.php`

### Tests for User Story 2

- [ ] T017 [US2] Update MarkNotificationAsReadControllerTest - remove Employee/Org setup, use User directly in `tests/Feature/Controllers/MarkNotificationAsReadControllerTest.php`
- [ ] T018 [P] [US2] Update MarkAllNotificationsAsReadControllerTest - remove Employee/Org setup, use User directly in `tests/Feature/Controllers/MarkAllNotificationsAsReadControllerTest.php`

**Checkpoint**: User Story 2 complete - users can mark notifications as read

---

## Phase 5: User Story 3 - Delete Notifications (Priority: P3)

**Goal**: Users can delete notifications they no longer need

**Independent Test**: Create notification for user, delete via UI/API, verify it no longer appears

### Implementation for User Story 3

- [ ] T019 [US3] Verify DeleteNotificationController works correctly (already uses #[CurrentUser]) in `app/Http/Controllers/DeleteNotificationController.php`

### Tests for User Story 3

- [ ] T020 [US3] Update DeleteNotificationControllerTest - remove Employee/Org setup, use User directly in `tests/Feature/Controllers/DeleteNotificationControllerTest.php`

**Checkpoint**: User Story 3 complete - users can delete notifications

---

## Phase 6: User Story 4 - Auto-Prune Old Notifications (Priority: P4)

**Goal**: Notifications older than 90 days are automatically pruned

**Independent Test**: Create notifications older than 90 days, run prune command, verify they are deleted

### Implementation for User Story 4

- [ ] T021 [US4] Verify Notification model prunable() method works without OrganizationScope in `app/Models/Notification.php`

### Tests for User Story 4

- [ ] T022 [US4] Update PruneOldNotificationsTest - remove Org scope references in `tests/Unit/Commands/PruneOldNotificationsTest.php`

**Checkpoint**: User Story 4 complete - old notifications are auto-pruned

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Final validation and cleanup

- [ ] T023 Run full test suite `composer test:unit`
- [ ] T024 Run static analysis `composer test:types`
- [ ] T025 Run code formatting `vendor/bin/pint --dirty`
- [ ] T026 Verify all notification operations work end-to-end via browser testing

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Skipped - no setup needed for refactor
- **Foundational (Phase 2)**: BLOCKS all user stories - must complete first
- **User Stories (Phase 3-6)**: All depend on Foundational phase completion
- **Polish (Phase 7)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - No dependencies on US1
- **User Story 3 (P3)**: Can start after Foundational (Phase 2) - No dependencies on US1/US2
- **User Story 4 (P4)**: Can start after Foundational (Phase 2) - No dependencies on other stories

### Within Each Phase

- Implementation before tests (since we're updating existing tests to match new implementation)
- Controller updates can run in parallel (different files)
- Test updates can run in parallel (different files)

### Parallel Opportunities

**Phase 2 (after T001-T002):**
```bash
# These can run in parallel:
T003: Update Employee model
T004: Update User model
T005: Delete HasNotifications trait
T006: Update NotificationFactory
T007: Delete EmployeeNotificationsQuery
T008: Delete EmployeeNotificationsQueryTest
T009: Delete EmployeeNotificationsTest
```

**Phase 4:**
```bash
# These can run in parallel:
T015: Update MarkNotificationAsReadController
T016: Update MarkAllNotificationsAsReadController
T017: Update MarkNotificationAsReadControllerTest
T018: Update MarkAllNotificationsAsReadControllerTest
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
2. Complete Phase 3: User Story 1
3. **STOP and VALIDATE**: Test viewing notifications works
4. Continue to remaining stories

### Incremental Delivery

1. Complete Foundational → Foundation ready
2. Add User Story 1 → Test independently → Notifications visible (MVP!)
3. Add User Story 2 → Test independently → Mark as read works
4. Add User Story 3 → Test independently → Delete works
5. Add User Story 4 → Test independently → Auto-prune works
6. Each story adds value without breaking previous stories

---

## Summary

| Phase | Tasks | Parallel Tasks |
|-------|-------|----------------|
| Phase 2: Foundational | 10 | 7 |
| Phase 3: US1 - View | 3 | 0 |
| Phase 4: US2 - Mark Read | 5 | 4 |
| Phase 5: US3 - Delete | 2 | 0 |
| Phase 6: US4 - Prune | 2 | 0 |
| Phase 7: Polish | 4 | 0 |
| **Total** | **26** | **11** |

---

## Notes

- This is a refactor - no new files created, only updates and deletions
- Frontend requires no changes (data structure unchanged)
- All user stories can be tested independently after foundational phase
- Run `composer test` after each phase to verify nothing is broken
