# Implementation Plan: Laravel Database Notifications

**Branch**: `004-add-laravel-database-notifications` | **Date**: 2025-11-22 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/004-add-laravel-database-notifications/spec.md`

## Summary

Implement Laravel database notifications to provide users with in-app notification capabilities. Users will view, manage (mark as read, delete), and track unread notifications via a header dropdown UI. The system will use Laravel's built-in database notification channel with automatic 90-day retention cleanup.

## Technical Context

**Language/Version**: PHP 8.4 with strict typing, TypeScript 5 for frontend
**Primary Dependencies**: Laravel 12, Inertia.js v2, React 18, shadcn/ui, Spatie Laravel Data
**Storage**: PostgreSQL (using Laravel's notifications table)
**Testing**: Pest v4 with 100% coverage requirement
**Target Platform**: Web application (browser)
**Project Type**: Web application (Laravel backend + React/Inertia frontend)
**Performance Goals**: Notifications load within 1 second, badge updates within 2 seconds
**Constraints**: Support 1000+ notifications per user without degradation
**Scale/Scope**: Multi-tenant SaaS application with existing User model

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Type Safety First | ✅ PASS | Will use explicit types in PHP and TypeScript, PHPStan Level 8 |
| II. Test Coverage | ✅ PASS | 100% coverage with Pest v4, will test all notification operations |
| III. Action Pattern | ✅ PASS | Actions for mark-read, mark-all-read, delete; Query for listing |
| IV. Laravel Conventions | ✅ PASS | Using Laravel's built-in notifications, Model::query(), Form Requests |
| V. Simplicity & YAGNI | ✅ PASS | Using Laravel's built-in notification system, no custom abstractions |

**Gate Result**: PASS - Proceed to Phase 0

## Project Structure

### Documentation (this feature)

```text
specs/004-add-laravel-database-notifications/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks command)
```

### Source Code (repository root)

```text
app/
├── Actions/
│   └── Notifications/
│       ├── MarkNotificationAsRead.php
│       ├── MarkAllNotificationsAsRead.php
│       └── DeleteNotification.php
├── Data/
│   └── NotificationData.php
├── Http/
│   ├── Controllers/
│   │   └── NotificationController.php
│   └── Requests/
│       └── Notifications/
├── Queries/
│   └── UserNotificationsQuery.php
└── Console/
    └── Commands/
        └── PruneOldNotifications.php

resources/js/
├── components/
│   └── notifications/
│       ├── notification-dropdown.tsx
│       ├── notification-item.tsx
│       └── notification-badge.tsx
└── pages/
    └── notifications/
        └── index.tsx (optional full page view)

database/
└── migrations/
    └── create_notifications_table.php (Laravel's built-in)

tests/
├── Feature/
│   └── Notifications/
│       ├── ViewNotificationsTest.php
│       ├── MarkNotificationAsReadTest.php
│       ├── MarkAllNotificationsAsReadTest.php
│       ├── DeleteNotificationTest.php
│       └── PruneOldNotificationsTest.php
└── Unit/
    └── Queries/
        └── UserNotificationsQueryTest.php
```

**Structure Decision**: Standard Laravel web application structure following PeopleDear's Action/Query pattern. Notifications components grouped in dedicated directories for clarity.

## Complexity Tracking

No violations to justify. Implementation uses Laravel's built-in notification system and follows established patterns.
