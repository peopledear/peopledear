# Tasks: TimeOff Type Processors

**Input**: Design documents from `/specs/002-timeoff-type-processors/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: Included per constitution requirement (100% coverage)

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3, US4)
- Include exact file paths in descriptions

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Create contracts and directory structure

- [ ] T001 Create TimeOffTypeValidator contract in app/Contracts/TimeOffTypeValidator.php
- [ ] T002 [P] Create TimeOffTypeProcessor contract in app/Contracts/TimeOffTypeProcessor.php
- [ ] T003 [P] Create Validators/TimeOffType directory structure
- [ ] T004 [P] Create Processors/TimeOffType directory structure

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Service provider bindings and base integration points

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [ ] T005 Create TimeOffServiceProvider for type resolution bindings in app/Providers/TimeOffServiceProvider.php
- [ ] T006 Register TimeOffServiceProvider in bootstrap/providers.php
- [ ] T007 [P] Update CreateTimeOffRequest to call validator in app/Actions/TimeOffRequest/CreateTimeOffRequest.php
- [ ] T008 [P] Update ApproveRequest to call processor in app/Actions/Approval/ApproveRequest.php
- [ ] T009 [P] Update CancelRequest to call reverse processor in app/Actions/Approval/CancelRequest.php

**Checkpoint**: Foundation ready - user story implementation can begin

---

## Phase 3: User Story 1 - Sick Leave Auto-Approval Processing (Priority: P1) 🎯 MVP

**Goal**: Sick leave requests auto-approve and processor records absence (no balance tracking)

**Independent Test**: Submit sick leave request and verify auto-approval with processor execution recording the absence

### Tests for User Story 1

- [ ] T010 [P] [US1] Create SickLeaveValidatorTest in tests/Unit/Validators/TimeOffType/SickLeaveValidatorTest.php
- [ ] T011 [P] [US1] Create SickLeaveProcessorTest in tests/Unit/Processors/TimeOffType/SickLeaveProcessorTest.php

### Implementation for User Story 1

- [ ] T012 [P] [US1] Create SickLeaveValidator in app/Validators/TimeOffType/SickLeaveValidator.php
- [ ] T013 [P] [US1] Create SickLeaveProcessor in app/Processors/TimeOffType/SickLeaveProcessor.php
- [ ] T014 [US1] Register SickLeave bindings in TimeOffServiceProvider

**Checkpoint**: User Story 1 complete - sick leave auto-approval with processing functional

---

## Phase 4: User Story 2 - Vacation Manual Approval Processing (Priority: P2)

**Goal**: Vacation requests require manager approval and deduct from vacation balance

**Independent Test**: Submit vacation request, approve as manager, verify balance deducted

### Tests for User Story 2

- [ ] T015 [P] [US2] Create VacationValidatorTest in tests/Unit/Validators/TimeOffType/VacationValidatorTest.php
- [ ] T016 [P] [US2] Create VacationProcessorTest in tests/Unit/Processors/TimeOffType/VacationProcessorTest.php

### Implementation for User Story 2

- [ ] T017 [P] [US2] Create VacationValidator in app/Validators/TimeOffType/VacationValidator.php
- [ ] T018 [P] [US2] Create VacationProcessor in app/Processors/TimeOffType/VacationProcessor.php
- [ ] T019 [US2] Register Vacation bindings in TimeOffServiceProvider

**Checkpoint**: User Story 2 complete - vacation approval workflow with balance deduction functional

---

## Phase 5: User Story 3 - Personal Day Approval Processing (Priority: P3)

**Goal**: Personal day requests require approval and deduct from personal day balance

**Independent Test**: Submit personal day request, approve, verify personal day balance deducted

### Tests for User Story 3

- [ ] T020 [P] [US3] Create PersonalDayValidatorTest in tests/Unit/Validators/TimeOffType/PersonalDayValidatorTest.php
- [ ] T021 [P] [US3] Create PersonalDayProcessorTest in tests/Unit/Processors/TimeOffType/PersonalDayProcessorTest.php

### Implementation for User Story 3

- [ ] T022 [P] [US3] Create PersonalDayValidator in app/Validators/TimeOffType/PersonalDayValidator.php
- [ ] T023 [P] [US3] Create PersonalDayProcessor in app/Processors/TimeOffType/PersonalDayProcessor.php
- [ ] T024 [US3] Register PersonalDay bindings in TimeOffServiceProvider

**Checkpoint**: User Story 3 complete - personal day approval workflow functional

---

## Phase 6: User Story 4 - Request Cancellation Processing (Priority: P4)

**Goal**: Cancelling approved requests reverses processor effects (restores balance or removes absence)

**Independent Test**: Approve a vacation request, cancel it, verify balance is restored

### Tests for User Story 4

- [ ] T025 [P] [US4] Create BereavementValidatorTest in tests/Unit/Validators/TimeOffType/BereavementValidatorTest.php
- [ ] T026 [P] [US4] Create BereavementProcessorTest in tests/Unit/Processors/TimeOffType/BereavementProcessorTest.php
- [ ] T027 [P] [US4] Create CancellationProcessingTest in tests/Feature/Controllers/CancellationProcessingTest.php

### Implementation for User Story 4

- [ ] T028 [P] [US4] Create BereavementValidator in app/Validators/TimeOffType/BereavementValidator.php
- [ ] T029 [P] [US4] Create BereavementProcessor in app/Processors/TimeOffType/BereavementProcessor.php
- [ ] T030 [US4] Register Bereavement bindings in TimeOffServiceProvider
- [ ] T031 [US4] Implement reverse() methods in all processors for cancellation

**Checkpoint**: User Story 4 complete - cancellation reversal functional for all types

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Integration testing and final validation

- [ ] T032 [P] Create end-to-end integration test in tests/Feature/TimeOffProcessingIntegrationTest.php
- [ ] T033 Run composer test:unit and fix any failures
- [ ] T034 Run composer test:types and fix any type errors
- [ ] T035 Run vendor/bin/pint --dirty and commit formatted code

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 completion - BLOCKS all user stories
- **User Stories (Phase 3-6)**: All depend on Foundational phase completion
  - US1 can start immediately after Foundational
  - US2-4 can run in parallel after US1 (no inter-story dependencies)
- **Polish (Phase 7)**: Depends on all user stories being complete

### Parallel Opportunities

**Phase 1**: T002, T003, T004 can run after T001
**Phase 2**: T007, T008, T009 can run in parallel
**Phase 3 (US1)**: T010, T011 in parallel, then T012, T013 in parallel
**Phase 4 (US2)**: T015, T016 in parallel, then T017, T018 in parallel
**Phase 5 (US3)**: T020, T021 in parallel, then T022, T023 in parallel
**Phase 6 (US4)**: T025, T026, T027 in parallel, then T028, T029 in parallel

---

## Parallel Example: User Story 2

```bash
# Launch all tests for User Story 2 together:
Task: "Create VacationValidatorTest in tests/Unit/Validators/TimeOffType/VacationValidatorTest.php"
Task: "Create VacationProcessorTest in tests/Unit/Processors/TimeOffType/VacationProcessorTest.php"

# Launch all implementations for User Story 2 together:
Task: "Create VacationValidator in app/Validators/TimeOffType/VacationValidator.php"
Task: "Create VacationProcessor in app/Processors/TimeOffType/VacationProcessor.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (contracts)
2. Complete Phase 2: Foundational (service provider, action updates)
3. Complete Phase 3: User Story 1 (sick leave)
4. **STOP and VALIDATE**: Test sick leave auto-approval end-to-end
5. Deploy/demo if ready

### Full Implementation

Continue with US2-4 and Polish phase after MVP validation.

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story
- Each user story is independently testable
- Commit after each task or logical group
- Stop at any checkpoint to validate
