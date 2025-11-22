# Research: Approvals Scaffold

**Branch**: `001-approvals-scaffold` | **Date**: 2025-11-22

## Overview

Technical context is well-defined (Laravel 12, React 18, PostgreSQL). This document captures key architectural decisions and patterns to follow.

## Decisions

### 1. Manager-Employee Relationship

**Decision**: Self-referential foreign key on Employee model

**Rationale**:
- Simple, single-table solution
- Employee can have one manager (another employee)
- Manager is just an employee with direct reports
- No separate "Manager" role table needed

**Alternatives Considered**:
- Separate managers table: Rejected - adds complexity, employees can be both
- Role-based: Already have roles, but manager relationship is organizational, not permission-based

### 2. Approval Model with Polymorphic Relationships

**Decision**: Create separate `Approval` model with polymorphic `approvable` relationship

**Rationale**:
- Extensible for future approvable types (overtime, expenses, etc.)
- Clean separation of approval logic from request data
- Single source of truth for all approval decisions
- Enables consistent audit trail across all approvable entities

**Schema**:
```
approvals
- id
- approvable_type (e.g., 'App\Models\TimeOffRequest')
- approvable_id
- status (pending, approved, rejected)
- approved_by (nullable, employee_id)
- approved_at (nullable)
- rejection_reason (nullable)
- timestamps
```

**Alternatives Considered**:
- Fields on TimeOffRequest: Rejected - not extensible for overtime/expenses
- Separate approval tables per type: Rejected - duplicates logic

### 3. Request Status Flow

**Decision**: Approval model owns the status, TimeOffRequest references it

**Rationale**:
- Approval status is the single source of truth
- TimeOffRequest can have `approval()` relationship
- Status changes happen through Approval actions

**States**: pending, approved, rejected, cancelled

### 4. Notification Approach

**Decision**: Use existing notification infrastructure (assumed available per spec assumptions)

**Rationale**:
- Spec assumes notification system exists
- Will use Laravel's notification system
- In-app notifications with optional email

### 5. Auto-Approval for Sick Leave

**Decision**: Create Approval record with status "approved" immediately for sick leave

**Rationale**:
- Consistent - all requests have an Approval record
- Check time-off type in CreateTimeOffRequest action
- Set approved_by to null (system auto-approved)
- Manager still notified for awareness

### 6. Approval Queue UI

**Decision**: Dedicated approvals page at `/approvals` with filtering

**Rationale**:
- Clear entry point for managers
- List view with filters (by type, date)
- Inline approve/reject actions
- Polymorphic design allows showing all approvable types in one queue

## Patterns to Follow

### Laravel Patterns

- **Actions**: ApproveRequest, RejectRequest, CancelRequest (work with Approval model)
- **Queries**: PendingApprovalsQuery with `builder()` method
- **Data Objects**: RejectRequestData for rejection reason transfer
- **Form Requests**: RejectRequestRequest for validation
- **Contextual Attributes**: `#[CurrentUser]` in controller methods
- **Polymorphic**: `MorphTo` on Approval, `MorphOne` on TimeOffRequest

### Frontend Patterns

- **Inertia Page**: `resources/js/pages/approvals/index.tsx`
- **Data Tables**: Use existing patterns for list with actions
- **Forms**: useForm hook for reject reason modal

## Dependencies

### Existing Code to Modify

- `app/Models/Employee.php` - add manager_id, manager() and directReports() relationships
- `app/Models/TimeOffRequest.php` - add approval() morphOne relationship
- `database/seeders/EmployeeSeeder.php` - add manager assignments
- `app/Actions/TimeOffRequest/CreateTimeOffRequest.php` - create Approval record (auto-approve if sick leave)

### New Code to Create

- `app/Models/Approval.php` - new model with polymorphic relationship
- Migration for approvals table
- Migration for manager_id on employees
- 3 new Actions (ApproveRequest, RejectRequest, CancelRequest)
- 1 new Query (PendingApprovalsQuery)
- 1 new Controller (ApprovalQueueController)
- 1 new Form Request (RejectRequestRequest)
- 1 new Inertia page (approvals/index.tsx)
- Tests for all new code
