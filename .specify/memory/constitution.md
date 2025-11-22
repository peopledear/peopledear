<!--
SYNC IMPACT REPORT
==================
Version change: 0.0.0 → 1.0.0 (initial ratification)

Modified principles: N/A (new constitution)

Added sections:
- Core Principles (5 principles)
- Technology Standards
- Development Workflow
- Governance

Removed sections: N/A

Templates requiring updates:
- .specify/templates/plan-template.md ✅ (Constitution Check section aligns)
- .specify/templates/spec-template.md ✅ (Requirements format compatible)
- .specify/templates/tasks-template.md ✅ (Testing phases align with Test-First principle)

Follow-up TODOs: None
-->

# PeopleDear Constitution

## Core Principles

### I. Type Safety First

All code MUST use explicit type declarations. This is non-negotiable.

- Every method and function MUST have explicit return type declarations
- Every method parameter MUST have type hints
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) {}`
- No empty constructors with zero parameters
- TypeScript: Type all props, state, and function parameters; no `any` type
- PHPStan Level 8 compliance is mandatory

**Rationale**: Type safety catches errors at compile time, improves IDE support, and makes code self-documenting. Explicit types eliminate entire classes of runtime errors.

### II. Test Coverage (NON-NEGOTIABLE)

All code MUST maintain 100% test coverage. Tests are core to the application.

- Use Pest v4 for all tests: `php artisan make:test --pest <name>`
- NEVER remove tests without explicit approval
- Test happy paths, failure paths, and edge cases
- Use `createQuietly()` not `create()` to prevent model events
- Type hint everything: `test('example', function (): void { ... });`
- Use datasets for validation rule tests to reduce duplication

**Pre-commit requirements**:
1. Run `composer test:unit` (MUST pass with 100% coverage)
2. Run `vendor/bin/pint --dirty` (format code)
3. Run `composer test:types` (static analysis)

**Rationale**: Tests are the only reliable way to verify code works and continues to work. 100% coverage ensures no untested code paths can introduce bugs.

### III. Action Pattern

Business logic MUST live in Actions and Queries, NOT in models or controllers.

**Actions** (`app/Actions/`):
- Handle create and update operations
- MUST implement `handle()` method (NOT `__invoke()`)
- Wrap complex operations in `DB::transaction()`
- Created via: `php artisan make:action "{name}" --no-interaction`

**Queries** (`app/Queries/`):
- Handle read operations
- MUST implement `builder()` method returning Eloquent/Query Builder
- Names WITHOUT "Get" prefix: `UsersQuery` not `GetUsersQuery`

**Lean Models**:
- Models contain ONLY: relationships, accessors/mutators, casts, scopes
- Simple boolean helpers: `isAdmin()`, `isPending()`
- NO update methods in models—all updates in Action classes

**Rationale**: Separating business logic from data layer keeps models testable, maintainable, and focused on a single responsibility.

### IV. Laravel Conventions

Follow Laravel 12 framework best practices and patterns.

**Database & Models**:
- ALWAYS use `Model::query()` for all queries
- Use eager loading to prevent N+1 query problems
- Migrations: remove `down()`, no defaults, no `after()` method
- Use `$table->foreignIdFor(Model::class)` for foreign keys

**Controllers**:
- Flat hierarchy—no nested folders
- Use Form Request classes for validation
- Use Laravel 12 contextual attributes (`#[CurrentUser]`, `#[Config('key')]`)

**Configuration**:
- Use `config('app.name')` not `env('APP_NAME')`
- Environment variables only in config files

**Artisan**:
- Use `php artisan make:` for all file creation
- Always pass `--no-interaction` and appropriate options

**Rationale**: Consistent patterns reduce cognitive load, make code predictable, and leverage battle-tested framework features.

### V. Simplicity & YAGNI

Avoid over-engineering. Only make changes that are directly requested or clearly necessary.

**Rules**:
- Don't add features, refactor code, or make "improvements" beyond what was asked
- Don't add error handling for scenarios that can't happen
- Don't create helpers or abstractions for one-time operations
- Don't design for hypothetical future requirements
- Reuse existing components before creating new ones
- Prefer editing existing files to creating new ones

**Code Review Check**:
- Is this complexity justified?
- What simpler alternative was rejected and why?

**Rationale**: Every abstraction has a cost. The right amount of complexity is the minimum needed for the current task.

## Technology Standards

**Backend Stack**:
- PHP 8.4+ with strict typing (`declare(strict_types=1)`)
- Laravel 12 framework
- PostgreSQL database
- Spatie Laravel Data for DTOs (NOT for validation)
- PHPStan Level 8 for static analysis
- Pest v4 for testing

**Frontend Stack**:
- React 18 with TypeScript 5
- Inertia.js v2 (NOT Vue.js)
- Tailwind CSS v4
- shadcn/ui components
- Vite for bundling

**Quality Tools**:
- Laravel Pint for code formatting
- Larastan for Laravel-specific static analysis
- Rector for automated refactoring

## Development Workflow

**Git Workflow**:
- Always create feature branches: `git checkout -b feature/descriptive-name`
- Before branching: `git fetch && git pull origin main`
- Only merge to main after tests pass and code is reviewed

**Pre-commit Gates** (ALL must pass):
1. `composer test:unit` - 100% coverage required
2. `vendor/bin/pint --dirty` - Code formatted
3. `composer test:types` - Static analysis passes

**Key Commands**:
- `composer test` - Full test suite
- `composer lint` - Fix code style
- `composer dev` - Start all dev servers

**File Naming**:
- PHP: PascalCase for classes
- TypeScript/React: lowercase with hyphens (`user-profile.tsx`)
- Tests: match source file location

## Governance

This constitution supersedes all other development practices for PeopleDear. Amendments require:

1. Documentation of proposed change
2. Justification with clear rationale
3. Update to this constitution with version increment
4. Propagation to all dependent templates

**Version Policy**:
- MAJOR: Backward incompatible principle removals or redefinitions
- MINOR: New principle/section added or materially expanded
- PATCH: Clarifications, wording fixes, non-semantic refinements

**Compliance**:
- All PRs must verify compliance with these principles
- Complexity must be justified in Complexity Tracking table
- Pre-commit gates enforce technical compliance automatically

**Runtime Guidance**: See `CLAUDE.md` for detailed development guidelines and code examples.

**Version**: 1.0.0 | **Ratified**: 2025-11-22 | **Last Amended**: 2025-11-22
