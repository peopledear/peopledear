# Quickstart: Approvals Scaffold

**Branch**: `001-approvals-scaffold` | **Date**: 2025-11-22

## Prerequisites

- PeopleDear development environment running
- Database migrated with existing Employee and TimeOffRequest models
- At least one organization with employees seeded

## Setup Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- `manager_id` column on `employees` table
- `approvals` table with polymorphic relationship

### 2. Update Seeders

```bash
php artisan db:seed --class=EmployeeSeeder
```

Or reset and reseed:

```bash
php artisan migrate:fresh --seed
```

### 3. Verify Setup

```bash
php artisan tinker
```

```php
// Check manager relationships
$employee = Employee::query()->whereNotNull('manager_id')->first();
$employee->manager->name; // Should return manager's name

// Check approval creation
$request = TimeOffRequest::query()->first();
$request->approval; // Should return Approval model
```

## Testing the Feature

### As Employee

1. Log in as an employee with a manager assigned
2. Navigate to time-off request page
3. Submit a vacation or personal day request
4. Verify request shows "Pending Approval" status
5. Submit a sick leave request
6. Verify sick leave is automatically approved

### As Manager

1. Log in as an employee who has direct reports
2. Navigate to `/org/{org}/approvals`
3. View pending requests from direct reports
4. Approve a request - verify employee is notified
5. Reject a request with reason - verify employee sees reason

### Edge Cases to Test

- Employee without manager cannot submit vacation/personal day
- Manager can only see requests from direct reports
- Cannot approve/reject already processed requests
- Employee can cancel own pending request

## Key Files

### Backend

- `app/Models/Approval.php` - Polymorphic approval model
- `app/Models/Employee.php` - Added manager relationship
- `app/Actions/Approval/ApproveRequest.php` - Approve action
- `app/Actions/Approval/RejectRequest.php` - Reject action
- `app/Queries/PendingApprovalsQuery.php` - Manager's queue query
- `app/Http/Controllers/ApprovalQueueController.php` - Queue endpoints

### Frontend

- `resources/js/pages/org-approvals/index.tsx` - Approval queue page

### Tests

```bash
# Run all approval tests
php artisan test --filter=Approval

# Run specific test
php artisan test --filter=ApproveRequestTest
```

## Common Issues

### "Employee has no manager"

Ensure EmployeeSeeder assigns managers. Check:

```php
Employee::query()->whereNull('manager_id')->count();
// Should be small (only top-level managers)
```

### "Approval not created"

TimeOffRequest creation should auto-create Approval. Check CreateTimeOffRequest action creates the Approval record.

### "Manager cannot see requests"

PendingApprovalsQuery filters by manager_id. Verify the employee submitting requests has their manager_id set to the logged-in manager.
