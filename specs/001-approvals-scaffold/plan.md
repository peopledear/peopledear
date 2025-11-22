# Implementation Plan: Approvals Scaffold

**Branch**: `001-approvals-scaffold` | **Date**: 2025-11-22 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/001-approvals-scaffold/spec.md`

## Summary

Implement an approval workflow system for time-off requests. Add manager-employee relationships to the Employee model, update seeders, and create approval functionality where vacation and personal day requests require manager approval while sick leave is auto-approved. Includes manager approval queue UI for processing pending requests.

## Technical Context

**Language/Version**: PHP 8.4 with strict typing
**Primary Dependencies**: Laravel 12, Inertia.js v2, React 18, TypeScript 5, Spatie Laravel Data v4
**Storage**: PostgreSQL
**Testing**: Pest v4 with 100% coverage requirement
**Target Platform**: Web application (browser)
**Project Type**: Web application (Laravel backend + React/Inertia frontend)
**Performance Goals**: Standard web app (<1s page loads, <5s for notifications)
**Constraints**: Must follow Action pattern, lean models, PHPStan Level 8
**Scale/Scope**: Small-medium business (100s of employees, 1000s of requests)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Type Safety First | ✅ Pass | All new code will have explicit types, PHPStan Level 8 |
| II. Test Coverage (NON-NEGOTIABLE) | ✅ Pass | Tests for all Actions, Queries, and models |
| III. Action Pattern | ✅ Pass | ApproveRequest, RejectRequest, CancelRequest actions |
| IV. Laravel Conventions | ✅ Pass | Form Requests, Model::query(), contextual attributes |
| V. Simplicity & YAGNI | ✅ Pass | No multi-level approval chains, no delegation |

## Project Structure

### Documentation (this feature)

```text
specs/001-approvals-scaffold/
├── plan.md              # This file
├── spec.md              # Feature specification
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks command)
```

### Source Code (repository root)

```text
app/
├── Actions/
│   └── Approval/
│       ├── ApproveRequest.php
│       ├── RejectRequest.php
│       └── CancelRequest.php
├── Data/
│   └── PeopleDear/
│       └── Approval/
│           └── RejectRequestData.php
├── Http/
│   ├── Controllers/
│   │   └── ApprovalQueueController.php
│   └── Requests/
│       └── RejectRequestRequest.php
├── Models/
│   ├── Approval.php (new - polymorphic)
│   ├── Employee.php (add manager_id)
│   └── TimeOffRequest.php (add approval() relationship)
├── Queries/
│   └── PendingApprovalsQuery.php
└── Enums/
    └── PeopleDear/
        └── RequestStatus.php (existing)

database/
├── migrations/
│   ├── XXXX_add_manager_id_to_employees_table.php
│   └── XXXX_create_approvals_table.php
└── seeders/
    └── EmployeeSeeder.php (update with manager assignments)

resources/js/
└── pages/
    └── org-approvals/
        └── index.tsx (approval queue page)

tests/
└── Unit/
    ├── Actions/
    │   └── TimeOffRequest/
    │       ├── ApproveTimeOffRequestTest.php
    │       ├── RejectTimeOffRequestTest.php
    │       └── CancelTimeOffRequestTest.php
    ├── Models/
    │   └── EmployeeManagerTest.php
    └── Queries/
        └── PendingApprovalsQueryTest.php
```

**Structure Decision**: Standard Laravel web application structure. Backend Actions handle approval business logic, Queries handle reading pending approvals, React/Inertia pages for manager approval queue UI.

## Complexity Tracking

> No violations to justify - design follows all constitution principles.
