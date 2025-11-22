# Quickstart: TimeOff Type Processors

**Feature**: 002-timeoff-type-processors
**Date**: 2025-11-22

## Prerequisites

- Branch `001-approvals-scaffold` merged to main
- Existing models: TimeOffRequest, Approval, VacationBalance, Employee
- Existing actions: CreateTimeOffRequest, ApproveRequest, CancelRequest

## Setup Steps

### 1. Create Contracts

```bash
# Create contract interfaces
mkdir -p app/Contracts
```

Create `TimeOffTypeValidator.php` and `TimeOffTypeProcessor.php` interfaces.

### 2. Create Validators Directory

```bash
mkdir -p app/Validators/TimeOffType
```

Create validators for each type:
- VacationValidator.php
- SickLeaveValidator.php
- PersonalDayValidator.php
- BereavementValidator.php

### 3. Create Processors Directory

```bash
mkdir -p app/Processors/TimeOffType
```

Create processors for each type:
- VacationProcessor.php
- SickLeaveProcessor.php
- PersonalDayProcessor.php
- BereavementProcessor.php

### 4. Register Service Bindings

In `AppServiceProvider` or new `TimeOffServiceProvider`:

```php
// Bind validator resolution
// Bind processor resolution
```

### 5. Update Existing Actions

1. **CreateTimeOffRequest**: Add validator call
2. **ApproveRequest**: Add processor call
3. **CancelRequest**: Add reverse call

### 6. Run Tests

```bash
# Run all tests with coverage
composer test:unit

# Run type checks
composer test:types

# Format code
vendor/bin/pint --dirty
```

## Development Flow

### TDD Approach (Per User Story)

1. Write test for validator/processor
2. Create interface if not exists
3. Implement validator/processor
4. Run tests
5. Commit

### Recommended Implementation Order

1. **Phase 1**: Contracts and base infrastructure
2. **Phase 2**: Sick leave (simplest - auto-approve, no balance)
3. **Phase 3**: Vacation (manual approve, balance-tracked)
4. **Phase 4**: Personal day (similar to vacation)
5. **Phase 5**: Bereavement (similar to sick leave)
6. **Phase 6**: Cancellation reversal
7. **Phase 7**: Integration and polish

## Key Files to Modify

| File | Change |
|------|--------|
| `app/Actions/TimeOffRequest/CreateTimeOffRequest.php` | Add validator call |
| `app/Actions/Approval/ApproveRequest.php` | Add processor call |
| `app/Actions/Approval/CancelRequest.php` | Add reverse call |
| `app/Providers/AppServiceProvider.php` | Register bindings |

## Verification

After implementation:

1. Submit sick leave → Auto-approved, absence recorded
2. Submit vacation → Pending, balance checked
3. Approve vacation → Balance deducted
4. Cancel vacation → Balance restored
5. All tests pass with 100% coverage
6. PHPStan Level 8 passes

## Common Issues

### Validator Not Found
- Check TimeOffType enum matches binding keys
- Verify service provider is registered

### Processor Transaction Failed
- Ensure DB::transaction wraps processor call
- Check balance update doesn't violate constraints

### Balance Mismatch
- Verify half-day calculations use 0.5
- Check concurrent request handling
