# Feature Specification: TimeOff Type Processors

**Feature Branch**: `002-timeoff-type-processors`
**Created**: 2025-11-22
**Status**: Draft
**Input**: User description: "TimeOff Types management. Each timeoff type must have its own validator and processor. The processor must be run after a timeoff is approved manually or automatically meaning that all timeoffrequests must have approvals ones depend on managers or owners and others can be automatically approved. The processors are actions that must implement a contract."

## Clarifications

### Session 2025-11-22

- Q: Does sick leave require a balance to be deducted, or is it unlimited/untracked? → A: Unlimited - No balance check, just record the absence
- Q: Is bereavement leave auto-approved like sick leave, or does it require manual approval? → A: Auto-approved - Like sick leave, no manager intervention
- Q: Is bereavement leave balance-tracked or unlimited? → A: Unlimited - No balance tracking, like sick leave

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Sick Leave Auto-Approval Processing (Priority: P1)

When an employee submits a sick leave request, it is automatically approved and the sick leave processor runs immediately to record the absence. Sick leave is unlimited and does not deduct from any balance.

**Why this priority**: Demonstrates the complete flow from submission through auto-approval to processing. Sick leave is the simplest case since it requires no manager intervention and no balance tracking, making it ideal for validating the processor architecture.

**Independent Test**: Can be fully tested by submitting a sick leave request and verifying the processor executes automatically, recording the absence.

**Acceptance Scenarios**:

1. **Given** an employee, **When** they submit a sick leave request, **Then** the request is auto-approved and the sick leave processor runs immediately
2. **Given** an employee submits sick leave, **When** the processor runs, **Then** the absence is recorded (no balance deduction)
3. **Given** an employee submits sick leave, **When** validation fails (e.g., dates in the past), **Then** the request is rejected before any approval occurs

---

### User Story 2 - Vacation Manual Approval Processing (Priority: P2)

When an employee submits a vacation request, it requires manager approval. Once approved, the vacation processor runs to deduct the vacation balance and complete post-approval tasks.

**Why this priority**: Vacation is the most common time-off type and demonstrates the full manual approval workflow. Depends on P1 infrastructure but adds the manual approval step.

**Independent Test**: Can be fully tested by submitting a vacation request, having a manager approve it, and verifying the processor executes only after approval.

**Acceptance Scenarios**:

1. **Given** an employee submits a vacation request, **When** the request is created, **Then** it remains pending until manager approval
2. **Given** a pending vacation request, **When** a manager approves it, **Then** the vacation processor runs and deducts the vacation balance
3. **Given** a pending vacation request, **When** a manager rejects it, **Then** no processor runs and the balance remains unchanged
4. **Given** an employee with insufficient vacation balance, **When** they submit a vacation request, **Then** the validator rejects the request before creating it
5. **Given** an employee requests vacation during a blackout period, **When** validation runs, **Then** the validator rejects the request with appropriate messaging

---

### User Story 3 - Personal Day Approval Processing (Priority: P3)

When an employee submits a personal day request, it requires approval. Once approved, the personal day processor runs to handle the post-approval logic specific to personal days.

**Why this priority**: Personal days follow the same approval flow as vacation but may have different validation rules (e.g., limited number per year). Lower priority as it reuses P2 infrastructure.

**Independent Test**: Can be fully tested by submitting a personal day request, having it approved, and verifying the personal day processor executes correctly.

**Acceptance Scenarios**:

1. **Given** an employee submits a personal day request, **When** the request is created, **Then** it remains pending until manager approval
2. **Given** a pending personal day request, **When** approved, **Then** the personal day processor runs
3. **Given** an employee has used their annual personal day allowance, **When** they submit another request, **Then** the validator rejects it

---

### User Story 4 - Request Cancellation Processing (Priority: P4)

When an approved time-off request is cancelled, a cancellation processor reverses the effects of the original processor (e.g., restoring balance).

**Why this priority**: Important for data integrity but depends on all previous stories being complete. Handles the reversal flow.

**Independent Test**: Can be fully tested by cancelling an approved request and verifying the balance is restored.

**Acceptance Scenarios**:

1. **Given** an approved vacation request, **When** the employee cancels it, **Then** the vacation balance is restored
2. **Given** an approved sick leave request, **When** the employee cancels it, **Then** the absence record is removed (no balance to restore)
3. **Given** a pending request, **When** the employee cancels it, **Then** no reversal processing occurs (nothing was deducted)

---

### Edge Cases

- What happens when a processor fails mid-execution? (Transaction should rollback, request stays approved but flagged for retry)
- What happens when an employee has no balance record for a time-off type? (System creates one with zero balance, validation fails)
- How does the system handle half-day requests for balance calculations? (Half-day deducts 0.5 from balance)
- What happens if a type has no configured processor? (Request is approved but no post-processing occurs, logged as warning)
- How are concurrent requests handled? (Validation checks balance at submission time; processor uses database transactions)

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST execute the appropriate validator when a time-off request is submitted
- **FR-002**: System MUST reject requests that fail validation before creating the approval record
- **FR-003**: System MUST execute the appropriate processor when a time-off request is approved (manually or automatically)
- **FR-004**: System MUST NOT execute processors for rejected or cancelled requests
- **FR-005**: All processors MUST implement a common contract defining the processing interface
- **FR-006**: All validators MUST implement a common contract defining the validation interface
- **FR-007**: System MUST support automatic approval for configured time-off types (sick leave, bereavement)
- **FR-008**: System MUST support manual approval requiring manager/owner action for configured types (vacation, personal day)
- **FR-009**: Processors MUST execute within a database transaction to ensure data integrity
- **FR-010**: System MUST deduct the appropriate balance when a balance-tracked request (vacation, personal day) is approved
- **FR-011**: System MUST restore the appropriate balance when an approved balance-tracked request is cancelled
- **FR-012**: Validators MUST check that the employee has sufficient balance for balance-tracked time-off types (vacation, personal day); sick leave is unlimited
- **FR-013**: System MUST log processor execution for auditing purposes

### Key Entities

- **TimeOffTypeValidator**: Contract that each time-off type validator must implement. Validates a request before it can be created.
- **TimeOffTypeProcessor**: Contract that each time-off type processor must implement. Executes post-approval tasks.
- **VacationBalance**: Existing entity that tracks available days for each time-off type per employee.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All time-off request submissions complete validation within 1 second
- **SC-002**: Processors execute within 3 seconds of approval
- **SC-003**: 100% of approved requests have their corresponding processor executed
- **SC-004**: Balance calculations are accurate for all approved and cancelled requests
- **SC-005**: System maintains data integrity with zero orphaned or inconsistent balance records
- **SC-006**: Failed processor executions are logged and can be retried without data corruption

## Assumptions

- Each TimeOffType enum value (Vacation, SickLeave, PersonalDay, Bereavement) will have a corresponding validator and processor
- VacationBalance entity already exists and can be extended to support all time-off types
- The existing approval workflow from `001-approvals-scaffold` is in place
- Bereavement leave is auto-approved and unlimited like sick leave (no balance tracking)
- Processors handle balance deduction; other post-processing (notifications, calendar sync) can be added later
- Validators are synchronous operations that return pass/fail with error messages
