# Feature Specification: User Time-Offs Page

**Feature Branch**: `006-user-timeoffs-page`
**Created**: 2025-11-26
**Status**: Draft
**Input**: User description: "user time offs page. we should have a page where the users will see a list of time off requests order by created_at desc, they should be able to filter by status and type. let's also use shadcn tables we should use a layout identical to overview page but with the time Time offs instead the user name."

## Clarifications

### Session 2025-11-26

- Q: Should pagination be included for this feature? → A: Yes, include pagination with 20 requests per page

## User Scenarios & Testing *(mandatory)*

### User Story 1 - View All My Time-Off Requests (Priority: P1)

As an employee, I want to view all my time-off requests in a single page so that I can track my request history and current status.

**Why this priority**: This is the core feature - without the list view, filtering has no purpose. Users need to see their requests first.

**Independent Test**: Can be fully tested by navigating to the time-offs page and verifying all the user's requests are displayed in a table format, ordered by most recent first.

**Acceptance Scenarios**:

1. **Given** I am a logged-in employee with existing time-off requests, **When** I navigate to the time-offs page, **Then** I see all my time-off requests displayed in a table ordered by creation date (newest first)
2. **Given** I am a logged-in employee with no time-off requests, **When** I navigate to the time-offs page, **Then** I see an empty state message indicating no requests exist
3. **Given** I am a logged-in employee, **When** I view the time-offs page, **Then** each request shows the time-off type, date range, and current status

---

### User Story 2 - Filter Time-Off Requests by Status (Priority: P2)

As an employee, I want to filter my time-off requests by status so that I can quickly find requests in a specific state (e.g., all pending requests).

**Why this priority**: Filtering by status is essential for employees who want to check on pending requests or review approved/rejected history.

**Independent Test**: Can be fully tested by selecting different status filter options and verifying only matching requests are displayed.

**Acceptance Scenarios**:

1. **Given** I am on the time-offs page with multiple requests in different statuses, **When** I select "Pending" from the status filter, **Then** only pending requests are displayed
2. **Given** I am on the time-offs page with the status filter active, **When** I clear the filter, **Then** all requests are displayed again
3. **Given** I am on the time-offs page, **When** I filter by a status that has no matching requests, **Then** I see an appropriate empty state message

---

### User Story 3 - Filter Time-Off Requests by Type (Priority: P3)

As an employee, I want to filter my time-off requests by type so that I can see all requests of a specific category (e.g., all vacation requests).

**Why this priority**: Type filtering helps employees review their usage of specific leave types (vacation days, sick leave, etc.).

**Independent Test**: Can be fully tested by selecting different type filter options and verifying only matching requests are displayed.

**Acceptance Scenarios**:

1. **Given** I am on the time-offs page with requests of different types, **When** I select "Vacation" from the type filter, **Then** only vacation requests are displayed
2. **Given** I am on the time-offs page, **When** I apply both status and type filters together, **Then** only requests matching both criteria are displayed
3. **Given** I am on the time-offs page with the type filter active, **When** I clear the filter, **Then** all requests are displayed again

---

### Edge Cases

- What happens when filtering returns zero results? Display an empty state with a clear message
- How does the page handle a user with hundreds of requests? Display 20 requests per page with pagination controls
- What happens if the user navigates to the page while not logged in? Redirect to login page

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST display a dedicated page listing all time-off requests for the currently logged-in employee
- **FR-002**: System MUST order time-off requests by creation date in descending order (newest first) by default
- **FR-003**: System MUST display each time-off request with: type, date range (start and end dates), and status
- **FR-004**: System MUST provide a filter control to filter requests by status (Pending, Approved, Rejected, Cancelled)
- **FR-005**: System MUST provide a filter control to filter requests by type (Vacation, Sick Leave, Personal Day, Bereavement)
- **FR-006**: System MUST allow combining status and type filters simultaneously
- **FR-007**: System MUST display requests in a table format with clear column headers
- **FR-008**: System MUST display an appropriate empty state when no requests exist or when filters return no results
- **FR-009**: System MUST persist filter selections in the URL for shareable/bookmarkable filtered views
- **FR-010**: System MUST display the page header with "Time Offs" title following the same layout pattern as the employee overview page
- **FR-011**: System MUST paginate results showing 20 requests per page with navigation controls to move between pages

### Key Entities

- **Time-Off Request**: Represents an employee's request for time away from work. Contains type (Vacation, Sick Leave, Personal Day, Bereavement), status (Pending, Approved, Rejected, Cancelled), start date, end date, and whether it's a half-day request.
- **Employee**: The user who owns the time-off requests

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can view their complete time-off request history within 2 seconds of page load
- **SC-002**: Users can filter requests and see results within 1 second of applying a filter
- **SC-003**: 100% of displayed requests show accurate type, date range, and status information
- **SC-004**: Users can find specific requests (by status or type) with no more than 2 interactions (select filter, view results)
- **SC-005**: Page maintains usability regardless of total request count via pagination (20 per page)

## Assumptions

- Users accessing this page are already authenticated employees
- The existing time-off request data model remains unchanged
- Filter changes will use URL query parameters for state management
- The page follows the same authentication and authorization patterns as other employee pages
- Status and type labels will use the existing translation/label system
