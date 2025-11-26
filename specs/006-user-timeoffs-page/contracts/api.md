# API Contracts: User Time-Offs Page

**Feature**: 006-user-timeoffs-page
**Date**: 2025-11-26

## Endpoints

### GET /time-offs

List all time-off requests for the current user with pagination and filtering.

**Route Name**: `employee.time-offs.index`

**Authentication**: Required (employee role)

**Query Parameters**:

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| page | integer | No | 1 | Current page number |
| status | integer | No | null | Filter by RequestStatus enum value (1-4) |
| type | integer | No | null | Filter by TimeOffType enum value (1-4) |

**Response**: Inertia page render

**Props Passed to Frontend**:

```typescript
interface TimeOffsPageProps {
    timeOffRequests: PaginatedResponse<TimeOffRequest>;
    types: EnumOptions;      // { 1: "Vacation", 2: "Sick Leave", ... }
    statuses: EnumOptions;   // { 1: "Pending", 2: "Approved", ... }
    filters: {
        status: number | null;
        type: number | null;
    };
}
```

**TimeOffRequest Shape** (from TimeOffRequestData):

```typescript
interface TimeOffRequest {
    id: number;
    type: number;
    status: number;
    startDate: string;      // ISO date string
    endDate: string | null; // ISO date string
    isHalfDay: boolean;
}
```

**PaginatedResponse Shape**:

```typescript
interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}
```

**Example Request**:
```
GET /time-offs?status=1&type=1&page=2
```

**Example Response Props**:
```json
{
    "timeOffRequests": {
        "data": [
            {
                "id": 42,
                "type": 1,
                "status": 1,
                "startDate": "2025-12-20",
                "endDate": "2025-12-27",
                "isHalfDay": false
            }
        ],
        "current_page": 2,
        "last_page": 5,
        "per_page": 20,
        "total": 87,
        "from": 21,
        "to": 40,
        "links": [
            { "url": null, "label": "&laquo; Previous", "active": false },
            { "url": "/time-offs?page=1", "label": "1", "active": false },
            { "url": "/time-offs?page=2", "label": "2", "active": true },
            { "url": "/time-offs?page=3", "label": "3", "active": false }
        ]
    },
    "types": {
        "1": "Vacation",
        "2": "Sick Leave",
        "3": "Personal Day",
        "4": "Bereavement"
    },
    "statuses": {
        "1": "Pending",
        "2": "Approved",
        "3": "Rejected",
        "4": "Cancelled"
    },
    "filters": {
        "status": 1,
        "type": 1
    }
}
```

## Error Responses

| Status | Condition | Behavior |
|--------|-----------|----------|
| 302 | Not authenticated | Redirect to login |
| 302 | No employee record | Redirect to organization-required |
| 200 | Success (even with empty results) | Render page with empty data array |

## Filter Behavior

1. **No filters applied**: Returns all user's requests, paginated
2. **Status filter only**: Returns requests matching status
3. **Type filter only**: Returns requests matching type
4. **Both filters**: Returns requests matching BOTH status AND type
5. **Invalid filter values**: Ignored (treated as no filter)
6. **Filter with empty results**: Returns empty data array with pagination metadata

## Pagination Behavior

1. **Page beyond last page**: Returns empty data (Laravel default behavior)
2. **Negative page number**: Treated as page 1
3. **Non-numeric page**: Treated as page 1
4. **Filter change**: Client should reset to page 1
