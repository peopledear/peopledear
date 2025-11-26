# Feature Specification: Refactor Notifications to User-Based

**Feature Branch**: `005-refactor-notifications`
**Created**: 2025-11-26
**Status**: Draft
**Input**: User description: "refactor notifications implementation. notifications must only be sent for users not employees, notifications don't need to be organization scoped. we need to touch migrations, models, actions, controllers and frontend."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - View Personal Notifications (Priority: P1)

As a user, I want to view my notifications in a dropdown menu so that I can stay informed about relevant events in the system without leaving my current page.

**Why this priority**: Core functionality - users need to see notifications before they can interact with them. This is the foundation for all notification features.

**Independent Test**: Can be fully tested by logging in as any user, triggering a notification, and verifying it appears in the dropdown with correct unread count.

**Acceptance Scenarios**:

1. **Given** a user with 3 unread notifications, **When** they click the bell icon, **Then** they see a dropdown with all 3 notifications sorted by unread first, then by creation date descending
2. **Given** a user with no notifications, **When** they click the bell icon, **Then** they see an empty state in the dropdown
3. **Given** a user with 5 unread notifications, **When** they view the dropdown, **Then** they see a badge showing "5" next to the unread filter

---

### User Story 2 - Mark Notifications as Read (Priority: P2)

As a user, I want to mark individual notifications or all notifications as read so that I can keep track of which notifications I've already reviewed.

**Why this priority**: Essential for notification management - users need to clear their unread count and manage notification state.

**Independent Test**: Can be tested by creating unread notifications for a user, marking them as read, and verifying the read status persists.

**Acceptance Scenarios**:

1. **Given** a user with an unread notification, **When** they click the "mark as read" action, **Then** the notification is marked as read and the unread count decreases by 1
2. **Given** a user with 5 unread notifications, **When** they click "Mark all as read", **Then** all notifications are marked as read and the unread count becomes 0
3. **Given** a user viewing a notification that is already read, **When** they click "mark as read" again, **Then** the notification remains read with no errors

---

### User Story 3 - Delete Notifications (Priority: P3)

As a user, I want to delete notifications I no longer need so that my notification list stays clean and relevant.

**Why this priority**: Important for user experience but not critical for core functionality. Users can work with notifications without deletion capability.

**Independent Test**: Can be tested by creating a notification for a user, deleting it, and verifying it no longer appears in their list.

**Acceptance Scenarios**:

1. **Given** a user with a notification, **When** they click the delete action, **Then** the notification is removed from their list immediately
2. **Given** a user deletes an unread notification, **When** the deletion completes, **Then** the unread count decreases by 1
3. **Given** a user in one browser session deletes a notification, **When** they view notifications in another session, **Then** the notification is not present

---

### User Story 4 - Auto-Prune Old Notifications (Priority: P4)

As a system administrator, I want old notifications to be automatically removed so that the database doesn't grow indefinitely with stale data.

**Why this priority**: Maintenance feature - important for long-term system health but not user-facing functionality.

**Independent Test**: Can be tested by creating notifications older than the retention period and running the prune command.

**Acceptance Scenarios**:

1. **Given** notifications older than 90 days exist, **When** the prune command runs, **Then** those notifications are deleted
2. **Given** notifications newer than 90 days exist, **When** the prune command runs, **Then** those notifications remain untouched

---

### Edge Cases

- What happens when a user tries to mark another user's notification as read? System must reject with 403 Forbidden
- What happens when a user tries to delete another user's notification? System must reject with 403 Forbidden
- How does the system handle concurrent mark-all-as-read requests? Should be idempotent with no errors
- What happens when polling fetches notifications while user is deleting one? Frontend should handle gracefully with optimistic updates

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST store notifications associated with User entities (not Employee entities)
- **FR-002**: System MUST NOT scope notifications by organization - notifications are user-specific only
- **FR-003**: System MUST allow users to view only their own notifications
- **FR-004**: System MUST display notifications sorted by unread status first, then by creation date descending
- **FR-005**: System MUST allow users to mark individual notifications as read
- **FR-006**: System MUST allow users to mark all their notifications as read in a single action
- **FR-007**: System MUST allow users to delete individual notifications
- **FR-008**: System MUST prevent users from accessing, modifying, or deleting other users' notifications
- **FR-009**: System MUST display the count of unread notifications in the UI
- **FR-010**: System MUST poll for new notifications at regular intervals (existing 15-second polling)
- **FR-011**: System MUST automatically prune notifications older than 90 days

### Key Entities

- **Notification**: A message sent to a user containing a title, message, optional action URL, and read status. Belongs to a single User. Contains created_at timestamp for sorting and pruning.
- **User**: The recipient of notifications. Has many notifications. Existing entity in the system.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All notification operations (view, mark read, delete) complete within 500ms for users with up to 100 notifications
- **SC-002**: Users can only access their own notifications - 100% authorization enforcement
- **SC-003**: Notification dropdown renders correctly on both desktop and mobile viewports
- **SC-004**: Polling updates notification list without page refresh and without disrupting user interactions
- **SC-005**: Database migration successfully removes organization_id column without data loss for notification content

## Assumptions

- The existing User model already has the `Notifiable` trait or can be configured to use it
- The notification dropdown UI component will be retained with minimal visual changes
- The 15-second polling interval is acceptable and will be retained
- The 90-day retention period for notifications is appropriate
- All existing notification types will continue to work after the refactor (just sent to users instead of employees)

## Out of Scope

- Creating new notification types
- Email or push notification channels
- User preferences for notification settings
- Notification grouping or categorization
- Real-time notifications via WebSockets (current polling approach retained)
