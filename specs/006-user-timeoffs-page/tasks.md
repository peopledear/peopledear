# Tasks: User Time-Offs Page

**Input**: Design documents from `/specs/006-user-timeoffs-page/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: Required (100% coverage per constitution)

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Backend**: `app/`, `routes/`, `tests/`
- **Frontend**: `resources/js/`
- Paths follow Laravel + Inertia.js conventions per plan.md

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install shadcn components and add TypeScript types needed by all user stories

- [x] T001 Install shadcn table component via `npx shadcn@latest add table` in resources/js/components/ui/table.tsx
- [x] T002 Install shadcn pagination component via `npx shadcn@latest add pagination` in resources/js/components/ui/pagination.tsx
- [x] T003 Add PaginatedResponse<T> and PaginationLink interfaces in resources/js/types/index.d.ts

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Create the base query class, controller, and route that all user stories depend on

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T004 Create EmployeeTimeOffRequestsQuery class skeleton in app/Queries/EmployeeTimeOffRequestsQuery.php with builder() method returning TimeOffRequest::query() for current user's employee, ordered by created_at DESC
- [x] T005 Create EmployeeTimeOffController class in app/Http/Controllers/EmployeeTimeOffController.php with index() method that injects EmployeeTimeOffRequestsQuery and returns Inertia::render()
- [x] T006 Add route GET /time-offs in routes/web.php inside employee group with name 'time-offs.index'
- [x] T007 [P] Create unit test file tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php with test for builder() returning correct base query ordered by created_at DESC
- [x] T008 [P] Create feature test file tests/Feature/Controllers/EmployeeTimeOffControllerTest.php with test for authenticated employee accessing the page

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - View All My Time-Off Requests (Priority: P1) 🎯 MVP

**Goal**: Employee can see all their time-off requests in a paginated table ordered by newest first

**Independent Test**: Navigate to /time-offs as authenticated employee, verify table displays requests with type, date range, and status columns, paginated at 20 per page

### Tests for User Story 1

- [x] T009 [US1] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: employee sees paginated time-off requests (20 per page)
- [x] T010 [US1] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: page displays empty state when user has no requests
- [x] T011 [US1] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: requests are ordered by created_at DESC
- [x] T012 [US1] Add test in tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php: builder paginates to 20 results

### Implementation for User Story 1

- [x] T013 [US1] Update EmployeeTimeOffController::index() in app/Http/Controllers/EmployeeTimeOffController.php to pass paginated timeOffRequests (20 per page), types, and statuses props using TimeOffRequestData::collect()
- [x] T014 [US1] Create page component resources/js/pages/employee-time-offs/index.tsx with TimeOffsPageProps interface, EmployeeLayout, and page header matching employee-overview pattern with "Time Offs" title
- [x] T015 [US1] Add shadcn Table to resources/js/pages/employee-time-offs/index.tsx displaying columns: Type, Date Range (start - end), Status (as Badge)
- [x] T016 [US1] Add pagination controls to resources/js/pages/employee-time-offs/index.tsx using shadcn Pagination component with Inertia router.get() navigation
- [x] T017 [US1] Add empty state component to resources/js/pages/employee-time-offs/index.tsx showing message when no requests exist
- [x] T018 [US1] Run tests and verify User Story 1 passes: `php artisan test --filter=EmployeeTimeOff`

**Checkpoint**: User Story 1 complete - employees can view their time-off requests in a paginated table

---

## Phase 4: User Story 2 - Filter by Status (Priority: P2)

**Goal**: Employee can filter their time-off requests by status (Pending, Approved, Rejected, Cancelled)

**Independent Test**: Select "Pending" from status filter, verify only pending requests are displayed; clear filter, verify all requests return

### Tests for User Story 2

- [x] T019 [US2] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: filtering by status returns only matching requests
- [x] T020 [US2] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: clearing status filter returns all requests
- [x] T021 [US2] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: status filter persists in URL query params
- [x] T022 [US2] Add test in tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php: withStatus() filters by RequestStatus enum value

### Implementation for User Story 2

- [x] T023 [US2] Add withStatus(?int $status) method to EmployeeTimeOffRequestsQuery in app/Queries/EmployeeTimeOffRequestsQuery.php that filters builder by status when provided
- [x] T024 [US2] Update EmployeeTimeOffController::index() in app/Http/Controllers/EmployeeTimeOffController.php to read status from request query and apply withStatus() filter, pass filters prop to frontend
- [x] T025 [US2] Add status filter Select component to resources/js/pages/employee-time-offs/index.tsx using shadcn Select with options from statuses prop
- [x] T026 [US2] Implement handleStatusChange() in resources/js/pages/employee-time-offs/index.tsx using router.get() with preserveState, reset to page 1 on filter change
- [x] T027 [US2] Run tests and verify User Story 2 passes: `php artisan test --filter=EmployeeTimeOff`

**Checkpoint**: User Story 2 complete - employees can filter by status

---

## Phase 5: User Story 3 - Filter by Type (Priority: P3)

**Goal**: Employee can filter their time-off requests by type (Vacation, Sick Leave, Personal Day, Bereavement)

**Independent Test**: Select "Vacation" from type filter, verify only vacation requests are displayed; apply both status and type filters, verify combined filtering works

### Tests for User Story 3

- [x] T028 [US3] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: filtering by type returns only matching requests
- [x] T029 [US3] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: combined status and type filters return correct results
- [x] T030 [US3] Add test in tests/Feature/Controllers/EmployeeTimeOffControllerTest.php: type filter persists in URL query params
- [x] T031 [US3] Add test in tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php: withType() filters by TimeOffType enum value

### Implementation for User Story 3

- [x] T032 [US3] Add withType(?int $type) method to EmployeeTimeOffRequestsQuery in app/Queries/EmployeeTimeOffRequestsQuery.php that filters builder by type when provided
- [x] T033 [US3] Update EmployeeTimeOffController::index() in app/Http/Controllers/EmployeeTimeOffController.php to read type from request query and apply withType() filter
- [x] T034 [US3] Add type filter Select component to resources/js/pages/employee-time-offs/index.tsx using shadcn Select with options from types prop
- [x] T035 [US3] Implement handleTypeChange() in resources/js/pages/employee-time-offs/index.tsx using router.get() with preserveState, reset to page 1 on filter change
- [x] T036 [US3] Run tests and verify User Story 3 passes: `php artisan test --filter=EmployeeTimeOff`

**Checkpoint**: User Story 3 complete - employees can filter by type and combine filters

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Final verification and code quality

- [x] T037 Run full test suite: `composer test:unit`
- [x] T038 Run static analysis: `composer test:types`
- [x] T039 Format code: `vendor/bin/pint --dirty`
- [ ] T040 Verify all acceptance scenarios from spec.md manually
- [ ] T041 Run quickstart.md validation steps

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-5)**: All depend on Foundational phase completion
  - US1 must complete before US2 (filter UI builds on base page)
  - US2 must complete before US3 (similar filter pattern)
- **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Depends on US1 completion (needs base page component to add filter to)
- **User Story 3 (P3)**: Depends on US2 completion (follows same filter pattern, adds alongside status filter)

### Within Each User Story

- Tests MUST be written and FAIL before implementation
- Query methods before controller updates
- Controller before frontend
- Story complete before moving to next priority

### Parallel Opportunities

**Phase 1 (Setup)**:
- T001 and T002 can run in parallel (different shadcn components)
- T003 can run in parallel with T001/T002 (different file)

**Phase 2 (Foundational)**:
- T007 and T008 can run in parallel (different test files)

**Within each User Story**:
- Tests (T009-T012, T019-T022, T028-T031) can be written in parallel before implementation

---

## Parallel Example: Phase 1 Setup

```bash
# Launch all setup tasks together:
Task: "Install shadcn table component"
Task: "Install shadcn pagination component"
Task: "Add TypeScript pagination types"
```

## Parallel Example: Phase 2 Foundational Tests

```bash
# Launch both test file creation tasks together:
Task: "Create unit test file tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php"
Task: "Create feature test file tests/Feature/Controllers/EmployeeTimeOffControllerTest.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (T001-T003)
2. Complete Phase 2: Foundational (T004-T008)
3. Complete Phase 3: User Story 1 (T009-T018)
4. **STOP and VALIDATE**: Test User Story 1 independently
5. Deploy/demo if ready - employees can view their requests

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo (status filtering)
4. Add User Story 3 → Test independently → Deploy/Demo (type filtering)
5. Polish phase → Final release

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Verify tests fail before implementing
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Constitution requires 100% test coverage - tests are included
