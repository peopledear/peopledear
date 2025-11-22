# Implementation Plan: TimeOff Type Processors

**Branch**: `002-timeoff-type-processors` | **Date**: 2025-11-22 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-timeoff-type-processors/spec.md`

## Summary

Implement a type-based validator and processor architecture for time-off requests. Each TimeOffType (Vacation, SickLeave, PersonalDay, Bereavement) will have its own validator (runs at submission) and processor (runs after approval). Validators and processors implement common contracts. Auto-approved types (sick leave, bereavement) are unlimited; manual approval types (vacation, personal day) are balance-tracked.

## Technical Context

**Language/Version**: PHP 8.4 with `declare(strict_types=1)`
**Primary Dependencies**: Laravel 12, Spatie Laravel Data v4, Inertia.js v2
**Storage**: PostgreSQL (existing VacationBalance table)
**Testing**: Pest v4 with 100% coverage requirement
**Target Platform**: Web application (Laravel + React)
**Project Type**: Web application (monorepo)
**Performance Goals**: Validation <1s, Processing <3s (from Success Criteria)
**Constraints**: PHPStan Level 8, database transactions for processors
**Scale/Scope**: Builds on existing approvals scaffold (001-approvals-scaffold)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Evidence |
|-----------|--------|----------|
| I. Type Safety First | ✅ PASS | Contracts enforce typed interfaces; all actions will have explicit types |
| II. Test Coverage | ✅ PASS | TDD approach: tests created before implementations per user story |
| III. Action Pattern | ✅ PASS | Validators and processors are Actions implementing contracts |
| IV. Laravel Conventions | ✅ PASS | Uses Model::query(), Form Requests, contextual attributes |
| V. Simplicity & YAGNI | ✅ PASS | Only implementing requested types; no speculative features |

**Pre-design Gate**: PASSED

## Project Structure

### Documentation (this feature)

```text
specs/002-timeoff-type-processors/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
│   └── api.md           # API endpoints
└── tasks.md             # Phase 2 output (via /speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Actions/
│   └── TimeOffRequest/
│       ├── CreateTimeOffRequest.php      # Existing (add validator call)
│       └── ProcessTimeOffRequest.php     # New: orchestrates processor execution
├── Contracts/
│   ├── TimeOffTypeValidator.php          # New: validator interface
│   └── TimeOffTypeProcessor.php          # New: processor interface
├── Validators/
│   └── TimeOffType/
│       ├── VacationValidator.php
│       ├── SickLeaveValidator.php
│       ├── PersonalDayValidator.php
│       └── BereavementValidator.php
├── Processors/
│   └── TimeOffType/
│       ├── VacationProcessor.php
│       ├── SickLeaveProcessor.php
│       ├── PersonalDayProcessor.php
│       └── BereavementProcessor.php
└── Http/
    └── Controllers/
        └── ApprovalQueueController.php   # Existing (add processor trigger)

tests/
├── Unit/
│   ├── Contracts/
│   │   ├── TimeOffTypeValidatorTest.php
│   │   └── TimeOffTypeProcessorTest.php
│   ├── Validators/
│   │   └── TimeOffType/
│   │       ├── VacationValidatorTest.php
│   │       ├── SickLeaveValidatorTest.php
│   │       ├── PersonalDayValidatorTest.php
│   │       └── BereavementValidatorTest.php
│   └── Processors/
│       └── TimeOffType/
│           ├── VacationProcessorTest.php
│           ├── SickLeaveProcessorTest.php
│           ├── PersonalDayProcessorTest.php
│           └── BereavementProcessorTest.php
└── Feature/
    └── Controllers/
        └── ApprovalQueueControllerProcessingTest.php
```

**Structure Decision**: Extends existing Laravel structure with new `Validators/` and `Processors/` directories to keep type-specific logic organized and consistent with the Action pattern.

## Complexity Tracking

No constitution violations requiring justification. Design follows established patterns.
