# Data Model: TimeOff Type Processors

**Feature**: 002-timeoff-type-processors
**Date**: 2025-11-22

## Entities

### TimeOffTypeValidator (Contract)

**Purpose**: Validates a time-off request before creation

```php
interface TimeOffTypeValidator
{
    /**
     * Validate the time-off request data.
     *
     * @param CreateTimeOffRequestData $data
     * @return ValidationResult
     * @throws ValidationException on failure
     */
    public function validate(CreateTimeOffRequestData $data): ValidationResult;
}
```

**Validation Result Fields**:
- `valid`: bool - Whether validation passed
- `errors`: array<string, string> - Field-specific error messages

### TimeOffTypeProcessor (Contract)

**Purpose**: Executes post-approval tasks for a time-off request

```php
interface TimeOffTypeProcessor
{
    /**
     * Process an approved time-off request.
     *
     * @param TimeOffRequest $request
     * @return void
     */
    public function process(TimeOffRequest $request): void;

    /**
     * Reverse the effects of processing (for cancellation).
     *
     * @param TimeOffRequest $request
     * @return void
     */
    public function reverse(TimeOffRequest $request): void;
}
```

### VacationBalance (Existing - Extended)

**Purpose**: Tracks available time-off days per employee per type

**Existing Fields** (from 001-approvals-scaffold):
- `id`: int
- `organization_id`: int (FK)
- `employee_id`: int (FK)
- `year`: int
- `total_days`: decimal(5,2)
- `used_days`: decimal(5,2)
- `created_at`: timestamp
- `updated_at`: timestamp

**Relationships**:
- Belongs to Organization
- Belongs to Employee

**Business Rules**:
- `available_days` = `total_days` - `used_days`
- Half-day requests deduct 0.5 from `used_days`
- Only vacation and personal day track balance
- Sick leave and bereavement do not use this entity

## Type-Specific Implementations

### VacationValidator

**Validations**:
- Start date not in the past
- End date >= start date
- Employee has sufficient vacation balance
- No overlapping approved vacation requests
- Not during blackout period (if configured)

### SickLeaveValidator

**Validations**:
- Start date not in the past
- End date >= start date
- No balance check (unlimited)

### PersonalDayValidator

**Validations**:
- Start date not in the past
- End date >= start date
- Employee has sufficient personal day balance
- Annual limit not exceeded

### BereavementValidator

**Validations**:
- Start date not in the past
- End date >= start date
- No balance check (unlimited)

### VacationProcessor

**Processing**:
- Deduct days from vacation balance
- Log processor execution

**Reversal**:
- Restore days to vacation balance
- Log reversal

### SickLeaveProcessor

**Processing**:
- Record absence (no balance deduction)
- Log processor execution

**Reversal**:
- Remove absence record
- Log reversal

### PersonalDayProcessor

**Processing**:
- Deduct days from personal day balance
- Log processor execution

**Reversal**:
- Restore days to personal day balance
- Log reversal

### BereavementProcessor

**Processing**:
- Record absence (no balance deduction)
- Log processor execution

**Reversal**:
- Remove absence record
- Log reversal

## State Transitions

### Request Lifecycle with Validation

```
[New Request]
    ↓
[Validate] → [Invalid] → [Rejected with errors]
    ↓ Valid
[Create Request + Approval]
    ↓
[Pending/Auto-Approved]
    ↓
[Manual Approve] or [Auto-Approved]
    ↓
[Process] → [Failed] → [Flagged for retry]
    ↓ Success
[Completed]
```

### Cancellation Flow

```
[Approved Request]
    ↓
[Cancel]
    ↓
[Reverse Processor] → [Failed] → [Flagged for retry]
    ↓ Success
[Cancelled]
```

## Data Integrity Rules

1. **Transaction Scope**: All processor operations wrapped in DB transaction
2. **Balance Constraints**: Cannot go negative (validation prevents)
3. **Audit Trail**: All processor executions logged
4. **Retry Safety**: Processors must be idempotent for retry scenarios
