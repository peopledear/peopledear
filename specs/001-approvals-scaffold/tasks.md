# Tasks: Approvals Scaffold

**Input**: Design documents from `/specs/001-approvals-scaffold/`
**Prerequisites**: plan.md, spec.md, data-model.md, contracts/

**Tests**: Tests are included as this project requires 100% test coverage per constitution.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3, US4, US5)
- Include exact file paths in descriptions

---

## Phase 1: Setup

**Purpose**: Database migrations and model foundation

- [ ] T001 Create migration for manager_id on employees in database/migrations/XXXX_add_manager_id_to_employees_table.php
- [ ] T002 Create migration for approvals table in database/migrations/XXXX_create_approvals_table.php
- [ ] T003 [P] Create Approval model in app/Models/Approval.php

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**CRITICAL**: No user story work can begin until this phase is complete

- [ ] T004 Add manager relationship to Employee model in app/Models/Employee.php
- [ ] T005 [P] Add approval relationship to TimeOffRequest model in app/Models/TimeOffRequest.php
- [ ] T006 [P] Create RejectRequestData in app/Data/PeopleDear/Approval/RejectRequestData.php
- [ ] T007 Update CreateTimeOffRequest action to create Approval record in app/Actions/TimeOffRequest/CreateTimeOffRequest.php

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Employee Has Assigned Manager (Priority: P1)

**Goal**: Employees have manager_id field and seeders create valid hierarchy

**Independent Test**: Verify employees have manager_id and seeders create valid assignments

### Tests for User Story 1

- [ ] T008 [P] [US1] Create EmployeeManagerTest in tests/Unit/Models/EmployeeManagerTest.php

### Implementation for User Story 1

- [ ] T009 [US1] Update EmployeeSeeder with manager hierarchy in database/seeders/EmployeeSeeder.php
- [ ] T010 [US1] Update EmployeeFactory to support manager_id in database/factories/EmployeeFactory.php

**Checkpoint**: User Story 1 complete - employees have managers assigned

---

## Phase 4: User Story 2 - Employee Submits Vacation Request (Priority: P2)

**Goal**: Vacation requests create pending approval and trigger notification

**Independent Test**: Submit vacation request and verify pending approval with manager notification

### Tests for User Story 2

- [ ] T011 [P] [US2] Create ApproveRequestTest in tests/Unit/Actions/Approval/ApproveRequestTest.php
- [ ] T012 [P] [US2] Create RejectRequestTest in tests/Unit/Actions/Approval/RejectRequestTest.php
- [ ] T013 [P] [US2] Create CancelRequestTest in tests/Unit/Actions/Approval/CancelRequestTest.php

### Implementation for User Story 2

- [ ] T014 [P] [US2] Create ApproveRequest action in app/Actions/Approval/ApproveRequest.php
- [ ] T015 [P] [US2] Create RejectRequest action in app/Actions/Approval/RejectRequest.php
- [ ] T016 [P] [US2] Create CancelRequest action in app/Actions/Approval/CancelRequest.php
- [ ] T017 [US2] Create RejectRequestRequest form request in app/Http/Requests/RejectRequestRequest.php

**Checkpoint**: User Story 2 complete - vacation approval workflow functional

---

## Phase 5: User Story 3 - Employee Submits Personal Day Request (Priority: P3)

**Goal**: Personal day requests follow same approval workflow as vacation

**Independent Test**: Submit personal day request and verify pending approval

### Implementation for User Story 3

- [ ] T018 [US3] Verify CreateTimeOffRequest handles personal_day type (already implemented in T007)

**Checkpoint**: User Story 3 complete - personal days route through approval

---

## Phase 6: User Story 4 - Employee Submits Sick Leave (Priority: P4)

**Goal**: Sick leave requests are auto-approved on submission

**Independent Test**: Submit sick leave and verify immediate approval status

### Tests for User Story 4

- [ ] T019 [P] [US4] Create test for auto-approval in tests/Unit/Actions/TimeOffRequest/CreateTimeOffRequestAutoApprovalTest.php

### Implementation for User Story 4

- [ ] T020 [US4] Ensure CreateTimeOffRequest auto-approves sick leave (implemented in T007, verify logic)

**Checkpoint**: User Story 4 complete - sick leave auto-approved

---

## Phase 7: User Story 5 - Manager Reviews Approval Queue (Priority: P5)

**Goal**: Managers see pending requests from direct reports and can approve/reject

**Independent Test**: Manager views queue with pending requests from their team

### Tests for User Story 5

- [ ] T021 [P] [US5] Create PendingApprovalsQueryTest in tests/Unit/Queries/PendingApprovalsQueryTest.php

### Implementation for User Story 5

- [ ] T022 [US5] Create PendingApprovalsQuery in app/Queries/PendingApprovalsQuery.php
- [ ] T023 [US5] Create ApprovalQueueController in app/Http/Controllers/ApprovalQueueController.php
- [ ] T024 [US5] Add approval routes to routes/web.php
- [ ] T025 [US5] Create approval queue page in resources/js/pages/org-approvals/index.tsx

**Checkpoint**: User Story 5 complete - managers can view and process approvals

---

## Phase 8: Polish & Cross-Cutting Concerns

**Purpose**: Notifications, validation, and cleanup

- [ ] T026 [P] Add notification when approval status changes
- [ ] T027 [P] Add overlap validation to prevent duplicate date requests
- [ ] T028 [P] Add authorization to prevent employees without manager from submitting
- [ ] T029 Run composer test:unit and fix any failures
- [ ] T030 Run composer test:types and fix any type errors

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 completion - BLOCKS all user stories
- **User Stories (Phase 3-7)**: All depend on Foundational phase completion
  - US1 can start immediately after Foundational
  - US2-5 can run in parallel after US1 (no inter-story dependencies)
- **Polish (Phase 8)**: Depends on all user stories being complete

### Parallel Opportunities

**Phase 1**: T002, T003 can run after T001
**Phase 2**: T005, T006 can run in parallel
**User Story 2**: T011-T013 (tests) in parallel, then T014-T016 (actions) in parallel
**User Story 5**: T021 (test) first, then T022-T025 sequentially

---

## Implementation Strategy

### MVP First (User Stories 1-2 Only)

1. Complete Phase 1: Setup (migrations)
2. Complete Phase 2: Foundational (models, relationships)
3. Complete Phase 3: User Story 1 (manager hierarchy)
4. Complete Phase 4: User Story 2 (vacation approval)
5. **STOP and VALIDATE**: Test end-to-end approval workflow
6. Deploy/demo if ready

### Full Implementation

Continue with US3-5 and Polish phase after MVP validation.

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story
- Each user story is independently testable
- Commit after each task or logical group
- Stop at any checkpoint to validate
