# API Contracts: TimeOff Type Processors

**Feature**: 002-timeoff-type-processors
**Date**: 2025-11-22

## Overview

This feature primarily extends existing endpoints rather than creating new ones. The validation and processing logic is internal to the Actions.

## Modified Endpoints

### POST /time-off/store

**Existing Endpoint** - Enhanced with validation

**Request Body** (unchanged):
```json
{
  "employee_id": 1,
  "organization_id": 1,
  "type": "vacation",
  "start_date": "2025-01-15",
  "end_date": "2025-01-17",
  "is_half_day": false
}
```

**New Behavior**:
- Runs type-specific validator before creating request
- Returns validation errors if validator fails

**Success Response** (unchanged):
- Status: 302 Redirect to employee.overview
- Session: `status: "Time off request submitted successfully."`

**New Error Response** (validation failure):
- Status: 422 Unprocessable Entity
- Session errors with field-specific messages

```json
{
  "errors": {
    "balance": "Insufficient vacation balance. You have 3 days available but requested 5 days.",
    "start_date": "Cannot request time off during company blackout period."
  }
}
```

### POST /org/approvals/{approval}/approve

**Existing Endpoint** - Enhanced with processing

**Request Body** (unchanged): Empty

**New Behavior**:
- After approval status updated, runs type-specific processor
- Processor executes within database transaction
- Returns error if processor fails

**Success Response** (unchanged):
- Status: 302 Redirect back
- Processor executed, balance updated (if applicable)

**New Error Response** (processor failure):
- Status: 500 Internal Server Error
- Approval remains approved but flagged for retry

### POST /org/approvals/{approval}/reject

**Existing Endpoint** - No changes

No processor runs on rejection.

## Internal Contracts

### TimeOffTypeValidator

Called by: `CreateTimeOffRequest` action

```php
public function validate(CreateTimeOffRequestData $data): ValidationResult;
```

**Input**: `CreateTimeOffRequestData` with all request fields
**Output**: `ValidationResult` with valid flag and errors array
**Throws**: `ValidationException` on critical failure

### TimeOffTypeProcessor

Called by: `ApproveRequest` action (process) and `CancelRequest` action (reverse)

```php
public function process(TimeOffRequest $request): void;
public function reverse(TimeOffRequest $request): void;
```

**Input**: Fully loaded `TimeOffRequest` model with relationships
**Output**: void (side effects on database)
**Throws**: `ProcessorException` on failure (triggers rollback)

## Type Resolution

Validators and processors are resolved based on `TimeOffType` enum:

| TimeOffType | Validator | Processor |
|-------------|-----------|-----------|
| Vacation | VacationValidator | VacationProcessor |
| SickLeave | SickLeaveValidator | SickLeaveProcessor |
| PersonalDay | PersonalDayValidator | PersonalDayProcessor |
| Bereavement | BereavementValidator | BereavementProcessor |

## Error Codes

| Code | Description | HTTP Status |
|------|-------------|-------------|
| VALIDATION_FAILED | Type-specific validation failed | 422 |
| INSUFFICIENT_BALANCE | Not enough days available | 422 |
| BLACKOUT_PERIOD | Request falls in blackout dates | 422 |
| PROCESSOR_FAILED | Post-approval processing error | 500 |
| REVERSAL_FAILED | Cancellation reversal error | 500 |
