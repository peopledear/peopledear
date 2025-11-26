# Data Model: User Time-Offs Page

**Feature**: 006-user-timeoffs-page
**Date**: 2025-11-26

## Existing Entities (No Changes Required)

This feature uses existing entities without modification.

### TimeOffRequest

**Table**: `time_off_requests`

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| organization_id | integer | FK to organizations |
| employee_id | integer | FK to employees |
| type | integer | TimeOffType enum (1=Vacation, 2=SickLeave, 3=PersonalDay, 4=Bereavement) |
| status | integer | RequestStatus enum (1=Pending, 2=Approved, 3=Rejected, 4=Cancelled) |
| start_date | date | Start of time off |
| end_date | date | End of time off (nullable for single day) |
| is_half_day | boolean | Whether this is a half-day request |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Relationships**:
- `belongsTo` Organization
- `belongsTo` Employee
- `hasOne` Approval (via HasApproval trait)

**Scopes**:
- `OrganizationScope` - Automatically filters by current organization

### Employee

**Table**: `employees`

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| user_id | integer | FK to users |
| organization_id | integer | FK to organizations |
| name | string | Employee name |

**Relationships**:
- `belongsTo` User
- `belongsTo` Organization
- `hasMany` TimeOffRequest

### Enums

**TimeOffType** (`App\Enums\PeopleDear\TimeOffType`):
- `Vacation = 1`
- `SickLeave = 2`
- `PersonalDay = 3`
- `Bereavement = 4`

**RequestStatus** (`App\Enums\PeopleDear\RequestStatus`):
- `Pending = 1`
- `Approved = 2`
- `Rejected = 3`
- `Cancelled = 4`

## Data Transfer Objects

### Existing: TimeOffRequestData

**Location**: `App\Data\PeopleDear\TimeOffRequest\TimeOffRequestData`

Already exists and will be reused for transforming TimeOffRequest models to frontend props.

## New Types (Frontend)

### PaginatedResponse<T>

Generic pagination wrapper for Laravel's LengthAwarePaginator serialization.

```typescript
interface PaginatedResponse<T> {
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

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
```

### TimeOffFilters

Filter state for the time-offs page.

```typescript
interface TimeOffFilters {
    status: number | null;
    type: number | null;
    page: number;
}
```

## Query Patterns

### EmployeeTimeOffRequestsQuery

New query class for fetching employee's time-off requests with filtering.

**Input Parameters**:
- Current user (via `#[CurrentUser]` attribute)
- Optional status filter (int|null)
- Optional type filter (int|null)

**Output**: `Builder<TimeOffRequest>` - Eloquent builder for pagination

**Default Ordering**: `created_at DESC` (newest first)

**Filters**:
- `status`: Exact match on RequestStatus enum value
- `type`: Exact match on TimeOffType enum value

## State Transitions

No state transitions are managed by this feature. The page is read-only and displays existing request states.
