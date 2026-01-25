# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PeopleDear is a people management application for tracking overtime, time-off, and expenses. It's a multi-tenant SaaS built with Laravel 12 + Inertia.js + React 19 + TypeScript.

## Development Commands

```bash
# Start all development services (server, queue, logs, vite)
composer dev

# Run full test suite (type coverage, unit tests, linting, static analysis)
composer test

# Run individual test suites
composer test:unit          # Pest tests with coverage
composer test:lint          # Pint, Rector, ESLint, Prettier
composer test:types         # PHPStan + TypeScript
composer test:type-coverage # Pest type coverage

# Run specific tests
php artisan test --compact tests/Feature/ExampleTest.php
php artisan test --compact --filter=testName

# Code formatting
composer lint               # Fix all (Rector, Pint, ESLint, Prettier)
vendor/bin/pint --dirty     # PHP formatting only

# Generate Wayfinder routes
composer wayfinder:generate
```

## Architecture

### Multi-Tenancy (Sprout)
- **Landlord routes**: `routes/web.php` - main domain routes
- **Tenant routes**: `routes/tenant.php` - organization subdomain routes (org.*, employee.*, settings.*)
- Organizations are tenants accessed via subdomains
- Use `#[CurrentTenant] Organization $organization` attribute to access current tenant

### Backend Patterns

**Actions** (`app/Actions/`) - Business logic in single-purpose classes with `handle()` method:
- Named without "Action" suffix: `CreateOrganization`, not `CreateOrganizationAction`
- Accept models as parameters for update/delete operations
- Always unit tested in `tests/Unit/Actions/`

**Queries** (`app/Queries/`) - Read-only data access with required `builder()` method:
- Injected at controller method level, not constructor
- Return `Builder<Model>` for chaining

**Data Objects** (`app/Data/`) - Spatie Laravel Data for request/response DTOs:
- Use `Optional` for partial updates with `toArray()` exclusion

**Controllers** (`app/Http/Controllers/`) - Thin HTTP adapters:
- Flat structure, no nested folders
- Method-level dependency injection
- Use `#[CurrentUser] User $user` attribute instead of `$request->user()`

### Frontend Structure

- **Pages**: `resources/js/pages/` - Inertia page components
- **Layouts**: `resources/js/layouts/` - App, Auth, Employee, Org layouts
- **Components**: `resources/js/components/` - Reusable components
- **UI**: `resources/js/components/ui/` - shadcn/ui components
- **Wayfinder routes**: `resources/js/wayfinder/` - Auto-generated type-safe routes

### Test Organization

```
tests/
├── Unit/              # Unit tests (Actions, Queries, Models)
├── Integration/       # Integration tests
├── Feature/
│   ├── Landlord/     # Landlord (main domain) feature tests
│   └── Tenant/       # Tenant (subdomain) feature tests
└── Browser/
    ├── Landlord/     # Landlord browser tests
    └── Tenant/       # Tenant browser tests
```

Use `test('description', function () {...})` syntax (imperative mood), never `it()`.

## Key Conventions

### PHP
- Always `declare(strict_types=1);`
- PHPDoc for all model properties and relationships with generics
- No `down()` method in migrations
- Use `foreignIdFor(Model::class)` for foreign keys
- No cascade constraints in migrations

### TypeScript/React
- Tailwind CSS v4 (use `@import "tailwindcss"`, not `@tailwind` directives)
- Use Inertia `<Form>` component with Wayfinder: `<Form {...store.form()}>`
- shadcn/ui components with Radix primitives

### Database
- PostgreSQL
- Column order: `id()`, `timestamps()`, then other columns
- Defaults in Model `$attributes` or Actions, not migrations
