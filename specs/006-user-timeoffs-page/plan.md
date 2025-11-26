# Implementation Plan: User Time-Offs Page

**Branch**: `006-user-timeoffs-page` | **Date**: 2025-11-26 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/006-user-timeoffs-page/spec.md`

## Summary

Create a dedicated page for employees to view all their time-off requests in a paginated table format with filtering by status and type. The page follows the employee overview page layout pattern with "Time Offs" as the title. Uses existing TimeOffRequest model and enum options.

## Technical Context

**Language/Version**: PHP 8.4 with `declare(strict_types=1)`, TypeScript 5
**Primary Dependencies**: Laravel 12, Inertia.js v2, React 18, shadcn/ui, Spatie Laravel Data v4
**Storage**: PostgreSQL (existing `time_off_requests` table)
**Testing**: Pest v4 with 100% coverage requirement
**Target Platform**: Web application (employee portal)
**Project Type**: Web application (Laravel + Inertia + React)
**Performance Goals**: Page load < 2 seconds, filter response < 1 second
**Constraints**: Must use existing data model, URL query params for filter state
**Scale/Scope**: Up to 100+ requests per user, 20 per page pagination

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Type Safety First | ✅ PASS | Will use typed PHP (Data objects), typed TypeScript interfaces |
| II. Test Coverage | ✅ PASS | Feature tests for controller, unit tests for query |
| III. Action Pattern | ✅ PASS | Using Query class for read operations (not Action) |
| IV. Laravel Conventions | ✅ PASS | Using `Model::query()`, Form Requests not needed (GET), contextual attributes |
| V. Simplicity & YAGNI | ✅ PASS | Minimal scope: list, filter, paginate - no extras |

**All gates pass. Proceeding to Phase 0.**

## Project Structure

### Documentation (this feature)

```text
specs/006-user-timeoffs-page/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (via /speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Http/Controllers/
│   └── EmployeeTimeOffController.php      # New controller for time-offs page
├── Queries/
│   └── EmployeeTimeOffRequestsQuery.php   # New query with filtering + pagination
└── Data/PeopleDear/TimeOffRequest/
    └── TimeOffRequestData.php             # Existing - reuse

resources/js/
├── pages/
│   └── time-offs/
│       └── index.tsx                      # New page component
├── components/ui/
│   └── table.tsx                          # New - shadcn table component
│   └── pagination.tsx                     # New - shadcn pagination component
└── types/
    └── index.d.ts                         # Extend with pagination types

routes/
└── web.php                                # Add route under employee group

tests/
├── Feature/Controllers/
│   └── EmployeeTimeOffControllerTest.php  # New feature tests
└── Unit/Queries/
    └── EmployeeTimeOffRequestsQueryTest.php # New unit tests
```

**Structure Decision**: Web application structure following existing Laravel + Inertia patterns. New controller under `employee.` route group, new page in `resources/js/pages/time-offs/`.

## Complexity Tracking

> No violations - no entries needed.
