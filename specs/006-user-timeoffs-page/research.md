# Research: User Time-Offs Page

**Feature**: 006-user-timeoffs-page
**Date**: 2025-11-26

## 1. Laravel Pagination with Inertia

### Decision
Use Laravel's `paginate()` method with Inertia's standard prop passing (not `Inertia::scroll()`).

### Rationale
- `paginate()` returns `LengthAwarePaginator` which provides total count, last page, and full navigation
- Standard pagination (not infinite scroll) matches the spec requirement for "pagination controls"
- `Inertia::scroll()` is for infinite scroll, which is not what we need

### Implementation Pattern
```php
// In Controller
return Inertia::render('time-offs/index', [
    'timeOffRequests' => TimeOffRequestData::collect(
        $query->builder()->paginate(20)
    ),
]);
```

### Paginator JSON Structure
When passed to frontend, Laravel's paginator serializes to:
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 20,
  "total": 100,
  "from": 1,
  "to": 20,
  "first_page_url": "...",
  "last_page_url": "...",
  "next_page_url": "...",
  "prev_page_url": "...",
  "links": [...]
}
```

### Alternatives Considered
- `Inertia::scroll()` - Rejected: For infinite scroll, not traditional pagination
- `simplePaginate()` - Rejected: Doesn't provide total count needed for "Page X of Y"
- `cursorPaginate()` - Rejected: Doesn't support page numbers in URL

---

## 2. URL Query Parameters for Filters

### Decision
Use Inertia's `router.get()` with query parameters, preserve via `only` option.

### Rationale
- FR-009 requires URL persistence for bookmarkable filtered views
- Inertia handles query params automatically when using `router.get()`
- Filters reset to page 1 when changed (standard UX pattern)

### Implementation Pattern
```tsx
// Frontend filter handler
const handleFilterChange = (key: string, value: string | null) => {
    router.get(route('employee.time-offs.index'), {
        ...filters,
        [key]: value,
        page: 1, // Reset to first page on filter change
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
```

```php
// Backend controller reads from request
public function index(Request $request): Response
{
    $status = $request->query('status');
    $type = $request->query('type');
    $page = $request->query('page', 1);
    // ...
}
```

### Alternatives Considered
- Client-side filtering only - Rejected: Doesn't work with pagination
- POST for filters - Rejected: Not bookmarkable, breaks browser back button

---

## 3. shadcn/ui Table and Pagination Components

### Decision
Add shadcn `table` and `pagination` components via CLI.

### Rationale
- Spec explicitly requests shadcn tables
- Components are already in the project's UI library pattern
- Provides consistent styling with existing components

### Installation Command
```bash
npx shadcn@latest add table pagination
```

### Table Component Structure
```tsx
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
```

### Pagination Component Structure
```tsx
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination"
```

### Alternatives Considered
- Custom table component - Rejected: Reinventing the wheel
- TanStack Table - Rejected: Over-engineered for simple list with filters

---

## 4. Query Class Pattern

### Decision
Create `EmployeeTimeOffRequestsQuery` following existing query patterns (consistent with `EmployeeOverviewController`).

### Rationale
- Constitution mandates Query classes for read operations
- Existing pattern in codebase shows correct approach
- Supports filtering and pagination via builder pattern

### Implementation Pattern
```php
final class EmployeeTimeOffRequestsQuery
{
    public function __construct(
        #[CurrentUser] private readonly ?User $user,
    ) {}

    public function builder(): Builder
    {
        return TimeOffRequest::query()
            ->where('employee_id', $this->user?->employee?->id)
            ->latest('created_at');
    }

    public function withStatus(?int $status): self
    {
        // Filter logic
        return $this;
    }

    public function withType(?int $type): self
    {
        // Filter logic
        return $this;
    }
}
```

### Alternatives Considered
- Inline query in controller - Rejected: Violates Action Pattern principle
- Model scope methods - Rejected: Less testable, harder to compose

---

## 5. TypeScript Pagination Types

### Decision
Extend existing types with Laravel pagination structure.

### Rationale
- Type safety is constitution principle #1
- Inertia passes paginator as plain object, needs typing
- Reusable across future paginated views

### Implementation Pattern
```typescript
// In types/index.d.ts
export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    links: PaginationLink[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
```

### Alternatives Considered
- `any` type - Rejected: Violates type safety principle
- Third-party types package - Rejected: Unnecessary dependency

---

## Summary

All research items resolved. No NEEDS CLARIFICATION items remain.

| Item | Decision | Risk Level |
|------|----------|------------|
| Pagination method | `paginate(20)` with LengthAwarePaginator | Low |
| Filter persistence | URL query params via Inertia router | Low |
| UI components | shadcn table + pagination | Low |
| Query pattern | New Query class with filter methods | Low |
| TypeScript types | Custom PaginatedResponse interface | Low |
