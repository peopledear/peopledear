# Research: TimeOff Type Processors

**Feature**: 002-timeoff-type-processors
**Date**: 2025-11-22

## Architectural Decisions

### 1. Contract-Based Validator/Processor Pattern

**Decision**: Use PHP interfaces (contracts) for validators and processors with type-specific implementations resolved via Laravel's service container.

**Rationale**:
- Follows existing Approvable contract pattern from 001-approvals-scaffold
- Enables type safety through explicit interface contracts
- Allows easy extension for new time-off types
- Leverages Laravel's dependency injection for clean resolution

**Alternatives Considered**:
- Strategy pattern with enum mapping: Rejected because contracts are more explicit and testable
- Single validator/processor with switch statements: Rejected for violating Single Responsibility Principle
- Event-driven processors: Overkill for synchronous processing needs

### 2. Validator Execution Point

**Decision**: Validators run in CreateTimeOffRequest action before creating the request and approval records.

**Rationale**:
- Prevents invalid data from entering the system
- Aligns with existing Form Request validation pattern
- Returns validation errors before any database operations
- Keeps validation close to the creation action

**Alternatives Considered**:
- Model observer: Rejected because it runs after save attempt
- Form Request validation: Insufficient for business rules like balance checking
- Separate validation endpoint: Unnecessary complexity

### 3. Processor Execution Point

**Decision**: Processors run in ApproveRequest action after approval status changes to Approved, wrapped in database transaction.

**Rationale**:
- Ensures processor only runs on successful approval
- Database transaction maintains data integrity
- Aligns with existing ApproveRequest action pattern
- Supports both manual and automatic approvals

**Alternatives Considered**:
- Queued job: Overkill for <3s processing requirement; adds complexity
- Model observer on Approval: Less explicit, harder to test
- Separate ProcessRequest action: Added indirection without benefit

### 4. Type Resolution Strategy

**Decision**: Use a registry/factory pattern with TimeOffType enum to resolve the correct validator/processor implementation.

**Rationale**:
- Single source of truth for type-to-class mapping
- Easy to add new types without modifying existing code
- Testable with mock implementations
- Follows Laravel service container patterns

**Implementation Approach**:
```php
// In AppServiceProvider or dedicated TimeOffServiceProvider
$this->app->bind(TimeOffTypeValidator::class, function ($app, $params) {
    return match ($params['type']) {
        TimeOffType::Vacation => $app->make(VacationValidator::class),
        TimeOffType::SickLeave => $app->make(SickLeaveValidator::class),
        // ...
    };
});
```

### 5. Balance Tracking Approach

**Decision**: Use existing VacationBalance model extended with type-aware balance tracking. Balance-tracked types: Vacation, PersonalDay. Unlimited types: SickLeave, Bereavement.

**Rationale**:
- Reuses existing infrastructure
- VacationBalance already has employee and organization relationships
- Simple extension to add type column or use existing type field

**Implementation Details**:
- Vacation processor: Deduct from vacation balance
- Personal day processor: Deduct from personal day balance
- Sick leave processor: Record absence only (no balance)
- Bereavement processor: Record absence only (no balance)

### 6. Cancellation Processing

**Decision**: Create type-specific reversal logic in processors via a `reverse()` method on the processor contract.

**Rationale**:
- Keeps reversal logic colocated with processing logic
- Each type knows how to undo its own effects
- Maintains symmetry between approve and cancel flows

**Implementation**:
```php
interface TimeOffTypeProcessor
{
    public function process(TimeOffRequest $request): void;
    public function reverse(TimeOffRequest $request): void;
}
```

## Integration Points

### Existing Code Modifications

1. **CreateTimeOffRequest**: Add validator call before creation
2. **ApproveRequest**: Add processor call after approval
3. **CancelRequest**: Add processor reverse call after cancellation

### New Components

1. **Contracts**: TimeOffTypeValidator, TimeOffTypeProcessor
2. **Validators**: 4 implementations (one per type)
3. **Processors**: 4 implementations (one per type)
4. **Service Provider**: Register type resolution bindings

## Testing Strategy

### Unit Tests
- Each validator tested in isolation with mock data
- Each processor tested with database transactions
- Contract compliance tests for all implementations

### Integration Tests
- End-to-end flow: create → validate → approve → process
- Cancellation flow: approve → cancel → reverse
- Auto-approval flow: create → auto-approve → process (immediate)

### Edge Case Tests
- Zero balance rejection
- Concurrent request handling
- Failed processor rollback
- Type without processor (warning logged)
