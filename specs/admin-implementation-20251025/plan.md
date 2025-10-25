# Admin Features - Implementation Plan

**Project:** PeopleDear
**Spec:** Admin Implementation
**Created:** 2025-10-25
**Strategy:** Test-Driven Development (TDD)

---

## High-Level Overview

Build admin interface for People Manager and Owner roles across 5 sequential phases, with each phase creating a PR, running full test suite, and merging to main before proceeding to the next.

### Phases Summary
1. **Admin Layout** - Navigation structure for admin pages
2. **Admin Dashboard** - Statistics and quick actions
3. **Admin Settings Layout** - Settings sidebar navigation with role-based sections
4. **Organization Settings** - Company info and office management (People Manager & Owner)
5. **System Settings** - User management, email, security, auth (Owner only)

---

## Architecture Decisions

### Frontend Architecture
- **Framework:** React with Inertia.js v2
- **Layouts:**
  - `admin-layout.tsx` - Main admin wrapper
  - `admin-settings-layout.tsx` - Settings-specific wrapper with sidebar
- **Pages Location:** `resources/js/pages/admin/`
- **Component Reuse:** Leverage existing UI components from `@/components/ui/`
- **Styling:** Tailwind v4 with dark mode support
- **Type Safety:** Full TypeScript coverage with proper typing

### Backend Architecture
- **Routes:** `/admin/*` prefix for all admin routes
- **Controllers:** Flat structure in `app/Http/Controllers/`
  - `AdminDashboardController` (invokable)
  - `AdminOrganizationController` - Organization CRUD
  - `AdminOfficeController` - Office CRUD
  - `AdminSystemSettingsController` - System settings (Owner only)
- **Business Logic:** Action classes in `app/Actions/`
- **Validation:** Data objects in `app/Data/`
- **Authorization:**
  - Middleware for route protection
  - Permission checks in controllers (`can:organizations.edit`, `can:settings.manage`)
  - Frontend conditional rendering based on permissions

### Database Schema

#### Settings
- Using **Spatie Laravel Settings** package
- Settings stored as typed classes (not database table)
- Each settings group is a dedicated class (e.g., `GeneralSettings`, `MailSettings`)
- Type-safe property access with IDE autocomplete
- Automatic caching and optimization

#### Organizations Table
```
- id (bigint, primary key)
- name (string)
- vat_number (string, nullable)
- ssn (string, nullable)
- phone (string, nullable)
- country (string, nullable)
- timestamps
```

#### Offices Table
```
- id (bigint, primary key)
- organization_id (foreign key)
- name (string)
- address_line1 (string)
- address_line2 (string, nullable)
- city (string)
- state (string, nullable)
- postal_code (string)
- country (string)
- phone (string, nullable)
- timestamps
```

---

## Testing Strategy (TDD Approach)

### Test Types Per Phase

#### Browser Tests (`tests/Browser/`)
- User navigation flows
- Form interactions
- Visual validation
- Role-based UI visibility
- Error state handling
- Success state handling

#### Feature Tests (`tests/Feature/`)
- Controller responses
- Authorization checks
- Form validation
- Route accessibility
- Middleware behavior

#### Unit Tests (`tests/Unit/`)
- Model relationships
- Data object validation
- Action business logic
- Edge cases and boundaries

### TDD Workflow
```
1. Write failing test (Red)
2. Implement minimal code to pass (Green)
3. Refactor for quality (Refactor)
4. Repeat
```

### Test Conventions
- Type hint all variables with PHPDoc
- Use `createQuietly()` for factory models
- Use `fresh()` for migration-seeded records
- Chain `expect()` methods
- Import all classes (no inline qualified names)

---

## Phase Breakdown

### Phase 1: Admin Layout & Navigation
**Complexity:** Low
**Estimated Effort:** Small
**Dependencies:** None

**Goal:** Create reusable admin layout with navigation

**Key Files:**
- `resources/js/layouts/admin-layout.tsx`
- `tests/Browser/AdminLayoutTest.php`

**Success Criteria:**
- Layout renders correctly for authorized users
- Navigation links present and functional
- Unauthorized users redirected
- Tests passing

---

### Phase 2: Admin Dashboard
**Complexity:** Medium
**Estimated Effort:** Medium
**Dependencies:** Phase 1

**Goal:** Dashboard with statistics and quick actions

**Key Files:**
- `app/Http/Controllers/AdminDashboardController.php`
- `resources/js/pages/admin/dashboard.tsx`
- `tests/Browser/AdminDashboardTest.php`
- `tests/Feature/Controllers/AdminDashboardControllerTest.php`

**Success Criteria:**
- Dashboard accessible at `/admin/dashboard`
- Three widgets rendered with placeholder data
- Authorization working
- Tests passing

---

### Phase 3: Admin Settings Layout
**Complexity:** Low
**Estimated Effort:** Small
**Dependencies:** Phase 1

**Goal:** Settings layout with sidebar navigation

**Key Files:**
- `resources/js/layouts/admin-settings-layout.tsx`
- `tests/Browser/AdminSettingsLayoutTest.php`

**Success Criteria:**
- Settings sidebar navigation working
- Owner sees System Settings section
- People Manager doesn't see System Settings section
- Both roles see Organization section
- Active section highlighting
- Tests passing

---

### Phase 4: Organization Settings (People Manager & Owner)
**Complexity:** High
**Estimated Effort:** Large
**Dependencies:** Phase 3

**Goal:** Organization and office management for People Manager and Owner roles

**Key Files:**
- Migration: `create_organizations_table.php`
- Migration: `create_offices_table.php`
- `app/Models/Organization.php`
- `app/Models/Office.php`
- Data objects for organization and office operations
- Actions for organization and office CRUD
- Controllers for organization and offices
- `resources/js/pages/admin/settings/organization.tsx`
- Multiple test files (unit, feature, browser)

**Success Criteria:**
- Organization CRUD working
- Office CRUD working (create, update, delete)
- People Manager can access
- Owner can access
- Employee cannot access
- Address validation working
- Tests passing

---

### Phase 5: System Settings (Owner Only)
**Complexity:** High
**Estimated Effort:** Large
**Dependencies:** Phase 3, Phase 4

**Goal:** System-level settings (user management, email, security, auth) for Owner role only

**Key Files:**
- Migration: `create_settings_table.php` (if not created in Phase 4)
- `app/Models/Setting.php`
- `app/Data/UpdateSystemSettingsData.php`
- `app/Actions/UpdateSystemSettings.php`
- `app/Http/Controllers/AdminSystemSettingsController.php`
- `resources/js/pages/admin/settings/system.tsx`
- Multiple test files (unit, feature, browser)

**Success Criteria:**
- System settings CRUD working
- Form validation working
- Settings persisted to database
- Owner-only access enforced
- People Manager cannot access (403)
- Success/error feedback
- Tests passing

---

## Git Workflow

### Per Phase
```bash
# Start phase
git fetch
git pull origin main
git checkout -b feature/[phase-name]

# TDD Development
# - Write tests first (failing)
# - Implement features
# - Make tests pass
# - Refactor

# Before PR
php artisan test           # Must pass
vendor/bin/pint --dirty   # Format code

# Commit
git add .
git commit -m "feat: [description]"
git push origin feature/[phase-name]

# Create PR with:
# - Summary of changes
# - Test coverage summary
# - Screenshots (if UI changes)

# After PR merged
git checkout main
git pull origin main
```

### Branch Naming Convention
- Phase 1: `feature/admin-layout`
- Phase 2: `feature/admin-dashboard`
- Phase 3: `feature/admin-settings-layout`
- Phase 4: `feature/organization-settings`
- Phase 5: `feature/system-settings`

---

## Technology Stack

### Backend
- **Laravel 12** - Framework
- **Spatie Laravel Permission** - Role/permission management
- **Spatie Laravel Data** - Data objects and validation
- **Spatie Laravel Settings** - Type-safe settings management
- **Pest 4** - Testing framework

### Frontend
- **React 18** - UI library
- **Inertia.js v2 (@inertiajs/react)** - Server-driven SPA
- **TypeScript 5** - Type safety
- **Tailwind v4** - Styling
- **shadcn/ui** - Component library (Radix UI + Tailwind)
- **Laravel Wayfinder** - Type-safe routing
- **Vite 6** - Build tool

### Development Tools
- **Laravel Pint** - Code formatting
- **PHPStan (Larastan)** - Static analysis
- **Rector** - Code quality
- **Playwright** - Browser testing

---

## Quality Assurance

### Code Quality Checks (Per Phase)
- [ ] All tests passing (`php artisan test`)
- [ ] No PHPStan errors (`vendor/bin/phpstan analyse`)
- [ ] Code formatted (`vendor/bin/pint --dirty`)
- [ ] No TypeScript errors (`npm run typecheck`)
- [ ] Build succeeds (`npm run build`)
- [ ] No console errors in browser tests

### Test Coverage Requirements
- **Browser Tests:** All user journeys and role-based visibility
- **Feature Tests:** All controller actions and authorization
- **Unit Tests:** All models, actions, and data objects

### Code Review Checklist
- Follows project conventions (CLAUDE.md)
- Proper type hints (PHPDoc and TypeScript)
- Authorization checks present
- Error handling comprehensive
- UI responsive and accessible
- Dark mode support

---

## Risk Mitigation

### Technical Risks
- **Risk:** Complex authorization logic
  - **Mitigation:** Test role/permission checks thoroughly

- **Risk:** UI complexity with role-based visibility
  - **Mitigation:** Component-based approach, clear permission checks

- **Risk:** Database migration conflicts
  - **Mitigation:** Always pull latest main before creating branch

### Process Risks
- **Risk:** Tests may be time-consuming
  - **Mitigation:** TDD catches issues early, saves debugging time

- **Risk:** Scope creep per phase
  - **Mitigation:** Strict adherence to phase boundaries

---

## Timeline Estimate

### Phase Duration (Approximate)
- Phase 1: 2-3 hours
- Phase 2: 4-6 hours
- Phase 3: 2-3 hours
- Phase 4: 6-8 hours
- Phase 5: 8-10 hours

**Total Estimated:** 22-30 hours

*Note: Times include test writing, implementation, debugging, and PR process*

---

## Next Steps

1. Review and approve this plan
2. Start Phase 1: Admin Layout
3. Follow TDD workflow
4. Create PR after each phase
5. Update `tasks.md` progress as we go
