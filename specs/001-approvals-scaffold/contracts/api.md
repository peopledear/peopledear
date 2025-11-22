# API Contracts: Approvals Scaffold

**Branch**: `001-approvals-scaffold` | **Date**: 2025-11-22

## Routes

### Approval Queue

| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | /org/{organization}/approvals | ApprovalQueueController@index | List pending approvals for manager |
| POST | /org/{organization}/approvals/{approval}/approve | ApprovalQueueController@approve | Approve a request |
| POST | /org/{organization}/approvals/{approval}/reject | ApprovalQueueController@reject | Reject a request |

### Employee Request Actions

| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| POST | /org/{organization}/approvals/{approval}/cancel | ApprovalQueueController@cancel | Cancel own pending request |

---

## Endpoints

### GET /org/{organization}/approvals

**Description**: List pending approvals for the current manager's direct reports.

**Authorization**: Must be authenticated, must have direct reports.

**Query Parameters**:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| type | string | No | Filter by approvable type (e.g., 'time-off') |
| status | string | No | Filter by status (default: pending) |

**Response** (Inertia):

```typescript
interface ApprovalQueueProps {
  approvals: {
    data: Approval[];
  };
  filters: {
    type: string | null;
    status: string;
  };
}

interface Approval {
  id: number;
  status: 'pending' | 'approved' | 'rejected' | 'cancelled';
  approved_at: string | null;
  rejection_reason: string | null;
  created_at: string;
  approvable: TimeOffRequest; // polymorphic
  employee: {
    id: number;
    name: string;
  };
}

interface TimeOffRequest {
  id: number;
  type: 'vacation' | 'sick_leave' | 'personal_day';
  start_date: string;
  end_date: string;
  reason: string | null;
}
```

---

### POST /org/{organization}/approvals/{approval}/approve

**Description**: Approve a pending request.

**Authorization**: Must be the manager of the employee who submitted the request.

**Request Body**: None

**Response**: Redirect back with success message.

**Side Effects**:
- Sets approval.status to 'approved'
- Sets approval.approved_by to current employee
- Sets approval.approved_at to now
- Notifies employee of approval

---

### POST /org/{organization}/approvals/{approval}/reject

**Description**: Reject a pending request with reason.

**Authorization**: Must be the manager of the employee who submitted the request.

**Request Body**:

```typescript
interface RejectRequest {
  rejection_reason: string; // required, min:1, max:500
}
```

**Validation Rules**:
- rejection_reason: required|string|min:1|max:500

**Response**: Redirect back with success message.

**Side Effects**:
- Sets approval.status to 'rejected'
- Sets approval.approved_by to current employee
- Sets approval.approved_at to now
- Sets approval.rejection_reason
- Notifies employee of rejection with reason

---

### POST /org/{organization}/approvals/{approval}/cancel

**Description**: Cancel own pending request.

**Authorization**: Must be the employee who submitted the request. Request must be pending.

**Request Body**: None

**Response**: Redirect back with success message.

**Side Effects**:
- Sets approval.status to 'cancelled'
- No notification sent (employee initiated)

---

## Error Responses

| Status | Code | Description |
|--------|------|-------------|
| 403 | Forbidden | Not authorized to approve/reject this request |
| 404 | Not Found | Approval not found |
| 422 | Unprocessable | Validation error (missing rejection_reason) |
| 422 | Unprocessable | Cannot modify non-pending approval |
