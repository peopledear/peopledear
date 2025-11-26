# Quickstart: User Time-Offs Page

**Feature**: 006-user-timeoffs-page
**Date**: 2025-11-26

## Prerequisites

1. Branch checked out: `006-user-timeoffs-page`
2. Dependencies installed: `composer install && npm install`
3. Database migrated: `php artisan migrate`
4. Dev server running: `composer run dev`

## Implementation Sequence

### Step 1: Add shadcn Components

```bash
npx shadcn@latest add table pagination
```

This installs:
- `resources/js/components/ui/table.tsx`
- `resources/js/components/ui/pagination.tsx`

### Step 2: Create Query Class

```bash
php artisan make:class "Queries/EmployeeTimeOffRequestsQuery" --no-interaction
```

Key implementation points:
- Inject `#[CurrentUser]` User
- Return `Builder<TimeOffRequest>`
- Add `withStatus(?int)` and `withType(?int)` filter methods
- Default order: `latest('created_at')`

### Step 3: Create Controller

```bash
php artisan make:controller EmployeeTimeOffController --no-interaction
```

Key implementation points:
- Single `index()` method
- Inject `EmployeeTimeOffRequestsQuery`
- Read filters from `$request->query()`
- Use `paginate(20)`
- Pass `TimeOffRequestData::collect()` to Inertia

### Step 4: Add Route

In `routes/web.php`, inside the `employee.` group:

```php
Route::get('/time-offs', [EmployeeTimeOffController::class, 'index'])
    ->name('time-offs.index');
```

### Step 5: Add TypeScript Types

In `resources/js/types/index.d.ts`:

```typescript
export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
```

### Step 6: Create Page Component

Create `resources/js/pages/time-offs/index.tsx`:

Key implementation points:
- Use `EmployeeLayout` with page header (similar to employee-overview)
- Use shadcn `Table` for data display
- Use shadcn `Select` for filters
- Use shadcn `Pagination` for navigation
- Handle empty state
- Use `router.get()` for filter changes

### Step 7: Write Tests

Feature test: `tests/Feature/Controllers/EmployeeTimeOffControllerTest.php`
- Test page loads for authenticated user
- Test filtering by status
- Test filtering by type
- Test combined filters
- Test pagination
- Test empty state

Unit test: `tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php`
- Test builder returns correct query
- Test status filter
- Test type filter
- Test ordering

## Verification

```bash
# Run tests
php artisan test --filter=EmployeeTimeOff

# Check types
composer test:types

# Format code
vendor/bin/pint --dirty
```

## Files Created/Modified

| File | Action |
|------|--------|
| `resources/js/components/ui/table.tsx` | Created (shadcn) |
| `resources/js/components/ui/pagination.tsx` | Created (shadcn) |
| `app/Queries/EmployeeTimeOffRequestsQuery.php` | Created |
| `app/Http/Controllers/EmployeeTimeOffController.php` | Created |
| `routes/web.php` | Modified |
| `resources/js/types/index.d.ts` | Modified |
| `resources/js/pages/time-offs/index.tsx` | Created |
| `tests/Feature/Controllers/EmployeeTimeOffControllerTest.php` | Created |
| `tests/Unit/Queries/EmployeeTimeOffRequestsQueryTest.php` | Created |
