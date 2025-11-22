# Feature Specification: Approvals Scaffold

**Feature Branch**: `001-approvals-scaffold`
**Created**: 2025-11-22
**Status**: Draft
**Input**: User description: "approvals scaffold for time-off requests. Vacations must be approved by managers, sick leave does not need approval, personal day does."

## Clarifications

### Session 2025-11-22

- Q: What happens when an employee submits a request for dates that overlap with an existing approved request? → A: Prevent submission (system blocks duplicates)
- Q: How does the system handle requests submitted when the manager is on vacation or unavailable? → A: Request remains pending until manager returns
- Q: What happens if an employee tries to submit a vacation request with insufficient balance? → A: Out of scope - balance validation is separate feature

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Employee Has Assigned Manager (Priority: P1)

Employees must have an assigned manager who can approve their requests. This is the foundation for the entire approval system.

**Why this priority**: Without manager-employee relationships, no approval workflow can function.

**Independent Test**: Can be fully tested by verifying employees have a manager_id field and seeders create valid manager assignments.

**Acceptance Scenarios**:

1. **Given** an employee record, **When** it is created or updated, **Then** it can have a manager assigned (another employee)
2. **Given** the seeder runs, **When** employees are created, **Then** they are assigned to appropriate managers based on organization structure
3. **Given** an employee with a manager, **When** the employee submits a request, **Then** the system can identify who should approve it

---

### User Story 2 - Employee Submits Vacation Request (Priority: P2)

An employee wants to request vacation time off and needs their manager's approval before the time off is granted.

**Why this priority**: Vacation requests are the most common type of time-off request and require the core approval workflow.

**Independent Test**: Can be fully tested by submitting a vacation request and verifying it appears in the manager's approval queue with pending status.

**Acceptance Scenarios**:

1. **Given** an employee with an assigned manager, **When** they submit a vacation request with dates and reason, **Then** the request is created with "pending approval" status and the manager is notified
2. **Given** a pending vacation request, **When** the manager views their approval queue, **Then** they see the request with employee name, dates, reason, and action buttons
3. **Given** a pending vacation request, **When** the manager approves it, **Then** the status changes to "approved" and the employee is notified
4. **Given** a pending vacation request, **When** the manager rejects it with a reason, **Then** the status changes to "rejected" and the employee is notified with the rejection reason

---

### User Story 3 - Employee Submits Personal Day Request (Priority: P3)

An employee wants to take a personal day and needs manager approval.

**Why this priority**: Personal days require approval like vacation but are a separate time-off category.

**Independent Test**: Can be fully tested by submitting a personal day request and verifying it routes through approval workflow.

**Acceptance Scenarios**:

1. **Given** an employee with an assigned manager, **When** they submit a personal day request with date and reason, **Then** the request is created with "pending approval" status
2. **Given** a pending personal day request, **When** the manager approves or rejects it, **Then** the appropriate status is set and employee is notified

---

### User Story 4 - Employee Submits Sick Leave (Priority: P4)

An employee needs to log sick leave which is automatically approved without manager intervention.

**Why this priority**: Sick leave follows a simpler flow (no approval needed) but must still be recorded in the system.

**Independent Test**: Can be fully tested by submitting sick leave and verifying it is immediately marked as approved.

**Acceptance Scenarios**:

1. **Given** an employee, **When** they submit a sick leave request with date(s), **Then** the request is automatically approved without requiring manager action
2. **Given** a sick leave request, **When** it is submitted, **Then** the manager is notified for awareness but no action is required

---

### User Story 5 - Manager Reviews Approval Queue (Priority: P5)

A manager needs to see all pending requests from their team members and take action on them efficiently.

**Why this priority**: This is the central interface for managers to process all approval requests.

**Independent Test**: Can be fully tested by creating multiple pending requests and verifying they appear in manager's queue.

**Acceptance Scenarios**:

1. **Given** a manager with team members who have pending requests, **When** they view their approval queue, **Then** they see all pending requests sorted by submission date
2. **Given** multiple pending requests, **When** the manager filters by request type (vacation/personal day), **Then** only matching requests are shown
3. **Given** a manager, **When** they approve or reject a request, **Then** the request is processed and the employee is notified

---

### Edge Cases

- What happens when an employee submits a request for dates that overlap with an existing approved request?
- How does the system handle requests submitted when the manager is on vacation or unavailable?
- What happens if an employee tries to submit a vacation request with insufficient balance?
- How are requests handled when an employee's manager changes mid-approval process?
- What happens if an employee has no manager assigned?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow employees to have a manager assigned (manager_id foreign key to another employee)
- **FR-002**: Seeders MUST create employees with valid manager assignments reflecting organization hierarchy
- **FR-003**: System MUST support three time-off request types: vacation, sick leave, and personal day
- **FR-004**: Vacation and personal day requests MUST require manager approval before being granted
- **FR-005**: Sick leave requests MUST be automatically approved upon submission without manager action
- **FR-006**: System MUST notify managers when a new request requiring approval is submitted
- **FR-007**: System MUST notify employees when their request status changes (approved/rejected)
- **FR-008**: Managers MUST be able to provide a reason when rejecting requests
- **FR-009**: System MUST track request status through states: pending, approved, rejected, cancelled
- **FR-010**: Employees MUST be able to cancel their own pending requests
- **FR-011**: Managers MUST only see and approve requests from employees they manage (direct reports)
- **FR-012**: System MUST prevent requests that overlap with existing approved requests for the same date(s)
- **FR-013**: System MUST record who approved/rejected each request and when
- **FR-014**: System MUST prevent employees without a manager from submitting requests that require approval

### Key Entities

- **Employee** (existing): Add manager_id field to establish manager-employee relationship
- **TimeOffRequest** (existing): Add approval-related fields (approved_by, approved_at, rejection_reason)
- **TimeOffType**: The existing enum (vacation, sick_leave, personal_day) with approval requirements

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Employees can submit any time-off request type in under 1 minute
- **SC-002**: Managers can approve/reject a request in under 30 seconds
- **SC-003**: 100% of requests requiring approval appear in manager's queue within 5 seconds of submission
- **SC-004**: Employees receive notification of status changes within 1 minute
- **SC-005**: Sick leave requests are automatically approved within 5 seconds of submission
- **SC-006**: Managers can process their entire approval queue in a single session without page reloads
- **SC-007**: System maintains complete audit trail for all approval decisions
- **SC-008**: 100% of seeded employees have valid manager assignments (except top-level managers)

## Assumptions

- Notification system (email or in-app) is available for sending alerts
- Time-off types and their approval requirements are fixed (not configurable by users)
- Vacation and personal day balance tracking exists or will be implemented separately
- One manager is sufficient for approval (no multi-level approval chains or delegation required for this scope)
- Requests remain pending indefinitely when manager is unavailable (no auto-escalation)
- Top-level managers (e.g., owners) do not require a manager assignment
- A manager is simply an employee who has other employees reporting to them
- Overtime requests will be implemented as a separate future feature
