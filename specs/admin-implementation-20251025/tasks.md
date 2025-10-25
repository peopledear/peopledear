# Admin Features - Detailed Tasks & Progress

**Project:** PeopleDear
**Spec:** Admin Implementation
**Last Updated:** 2025-10-25

---

## Progress Overview

- [ ] Phase 1: Admin Layout & Navigation
- [ ] Phase 2: Admin Dashboard
- [ ] Phase 3: Admin Settings Layout
- [ ] Phase 4: Organization Settings (People Manager & Owner)
- [ ] Phase 5: System Settings (Owner Only)

---

## Phase 1: Admin Layout & Navigation

**Branch:** `feature/admin-layout`
**Status:** Not Started

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/admin-layout`

### Browser Tests (TDD - Write First)
- [ ] Create test: `tests/Browser/AdminLayoutTest.php`
- [ ] Test: Admin layout renders for people_manager role
- [ ] Test: Admin layout renders for owner role
- [ ] Test: Admin layout redirects for employee role
- [ ] Test: Navigation menu displays correct items
- [ ] Test: Active page highlighting works
- [ ] Test: Mobile navigation works (responsive)
- [ ] Test: Dark mode toggle works

### Implementation
- [ ] Create layout: `resources/js/layouts/admin-layout.tsx`
- [ ] Add navigation items:
  - Dashboard link (`/admin/dashboard`)
  - Settings link (`/admin/settings`)
  - Users link (if applicable)
- [ ] Add breadcrumb support
- [ ] Add role-based navigation visibility
- [ ] Implement responsive design (mobile sidebar/drawer)
- [ ] Add dark mode support
- [ ] Style with Tailwind v4

### Verification
- [ ] All browser tests pass
- [ ] No TypeScript errors: `npm run typecheck`
- [ ] Build succeeds: `npm run build`
- [ ] Run all tests: `php artisan test`
- [ ] Run Pint: `vendor/bin/pint --dirty`
- [ ] No PHPStan errors: `vendor/bin/phpstan analyse`

### PR & Merge
- [ ] Commit changes with descriptive message
- [ ] Push branch: `git push origin feature/admin-layout`
- [ ] Create PR with summary and test results
- [ ] Address review feedback (if any)
- [ ] Merge to main
- [ ] Update this file: Mark Phase 1 as ✅

---

## Phase 2: Admin Dashboard

**Branch:** `feature/admin-dashboard`
**Status:** Not Started
**Dependencies:** Phase 1 completed

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/admin-dashboard`

### Browser Tests (TDD - Write First)
- [ ] Create test: `tests/Browser/AdminDashboardTest.php`
- [ ] Test: Dashboard accessible at `/admin/dashboard` for people_manager
- [ ] Test: Dashboard accessible for owner
- [ ] Test: Dashboard forbidden for employee role
- [ ] Test: User statistics widget displays
- [ ] Test: Organization overview widget displays
- [ ] Test: Quick actions widget displays
- [ ] Test: Quick action buttons are clickable

### Feature Tests (TDD - Write First)
- [ ] Create test: `tests/Feature/Controllers/AdminDashboardControllerTest.php`
- [ ] Test: Authorized users can access dashboard
- [ ] Test: Unauthorized users get 403
- [ ] Test: Dashboard returns correct Inertia props

### Implementation - Backend
- [ ] Create route: `Route::get('/admin/dashboard', AdminDashboardController::class)`
- [ ] Create controller: `php artisan make:controller AdminDashboardController --invokable`
- [ ] Add middleware: `->middleware(['auth', 'can:employees.view'])`
- [ ] Fetch user statistics (total, active, recent)
- [ ] Fetch organization overview data
- [ ] Return Inertia response with data

### Implementation - Frontend
- [ ] Create page: `resources/js/pages/admin/dashboard.tsx`
- [ ] Create UserStatisticsWidget component (or inline)
- [ ] Create OrganizationOverviewWidget component (or inline)
- [ ] Create QuickActionsWidget component (or inline)
- [ ] Use AdminLayout wrapper
- [ ] Add breadcrumbs
- [ ] Style with Tailwind v4 grid
- [ ] Add dark mode support
- [ ] Make responsive

### Verification
- [ ] All browser tests pass
- [ ] All feature tests pass
- [ ] Dashboard displays correctly in browser
- [ ] Authorization working (403 for unauthorized)
- [ ] No TypeScript errors
- [ ] Build succeeds
- [ ] Run all tests: `php artisan test`
- [ ] Run Pint: `vendor/bin/pint --dirty`
- [ ] No PHPStan errors

### PR & Merge
- [ ] Commit changes
- [ ] Push branch
- [ ] Create PR with screenshots
- [ ] Merge to main
- [ ] Update this file: Mark Phase 2 as ✅

---

## Phase 3: Admin Settings Layout

**Branch:** `feature/admin-settings-layout`
**Status:** Not Started
**Dependencies:** Phase 1 completed

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/admin-settings-layout`

### Browser Tests (TDD - Write First)
- [ ] Create test: `tests/Browser/AdminSettingsLayoutTest.php`
- [ ] Test: Settings layout renders for people_manager
- [ ] Test: Settings layout renders for owner
- [ ] Test: Settings sidebar shows all sections for owner
- [ ] Test: Settings sidebar hides Organizations for people_manager
- [ ] Test: Active section highlighting works
- [ ] Test: Mobile sidebar is collapsible

### Implementation
- [ ] Create layout: `resources/js/layouts/admin-settings-layout.tsx`
- [ ] Add sidebar navigation with sections:
  - General
  - User Management
  - Email Configuration
  - Organizations (conditional: owner only)
- [ ] Implement active section detection from current route
- [ ] Add permission-based conditional rendering
- [ ] Style sidebar with Tailwind v4
- [ ] Make responsive (collapsible on mobile)
- [ ] Add dark mode support

### Verification
- [ ] All browser tests pass
- [ ] Settings navigation works correctly
- [ ] Role-based visibility correct
- [ ] No TypeScript errors
- [ ] Build succeeds
- [ ] Run all tests: `php artisan test`
- [ ] Run Pint: `vendor/bin/pint --dirty`

### PR & Merge
- [ ] Commit changes
- [ ] Push branch
- [ ] Create PR
- [ ] Merge to main
- [ ] Update this file: Mark Phase 3 as ✅

---

## Phase 4: Organization Settings (People Manager & Owner)

**Branch:** `feature/organization-settings`
**Status:** Not Started
**Dependencies:** Phase 3 completed

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/organization-settings`

### Unit Tests (TDD - Write First)
- [ ] Create test: `tests/Unit/Models/OrganizationTest.php`
- [ ] Test: Organization has offices relationship
- [ ] Test: Organization model casts work correctly
- [ ] Create test: `tests/Unit/Models/OfficeTest.php`
- [ ] Test: Office belongs to organization relationship
- [ ] Test: Office model has correct attributes
- [ ] Create test: `tests/Unit/Actions/UpdateOrganizationTest.php`
- [ ] Test: Action updates organization correctly
- [ ] Create test: `tests/Unit/Actions/CreateOfficeTest.php`
- [ ] Test: Action creates office with correct data
- [ ] Create test: `tests/Unit/Actions/UpdateOfficeTest.php`
- [ ] Test: Action updates office correctly
- [ ] Create test: `tests/Unit/Actions/DeleteOfficeTest.php`
- [ ] Test: Action deletes office correctly

### Feature Tests (TDD - Write First)
- [ ] Create test: `tests/Feature/Controllers/AdminOrganizationControllerTest.php`
- [ ] Test: People manager can access organization settings
- [ ] Test: Owner can access organization settings
- [ ] Test: Employee cannot access (403)
- [ ] Test: Organization update succeeds
- [ ] Test: Validation errors returned correctly
- [ ] Create test: `tests/Feature/Controllers/AdminOfficeControllerTest.php`
- [ ] Test: People manager can create office
- [ ] Test: Owner can create office
- [ ] Test: People manager can update office
- [ ] Test: Owner can update office
- [ ] Test: People manager can delete office
- [ ] Test: Owner can delete office
- [ ] Test: Employee cannot manage offices (403)

### Browser Tests (TDD - Write First)
- [ ] Create test: `tests/Browser/OrganizationSettingsTest.php`
- [ ] Test: Organization settings page renders for people_manager
- [ ] Test: Organization settings page renders for owner
- [ ] Test: Page forbidden for employee (redirect or 403)
- [ ] Test: Organization form pre-populated
- [ ] Test: Organization form submission works
- [ ] Test: Offices list displays
- [ ] Test: Create office button works
- [ ] Test: Office creation form works
- [ ] Test: Office editing works
- [ ] Test: Office deletion with confirmation works
- [ ] Test: Validation errors display

### Implementation - Database
- [ ] Create migration: `php artisan make:migration create_organizations_table`
- [ ] Define schema: id, timestamps, name, vat_number, ssn, phone, country
- [ ] Remove `down()` method
- [ ] Create migration: `php artisan make:migration create_offices_table`
- [ ] Define schema: id, timestamps, organization_id (FK), name, address_line1, address_line2, city, state, postal_code, country, phone
- [ ] Use `foreignIdFor(Organization::class)`
- [ ] Remove `down()` method
- [ ] Run migrations: `php artisan migrate:fresh --seed`

### Implementation - Models
- [ ] Create model: `php artisan make:model Organization -f`
- [ ] Add fillable attributes
- [ ] Define `casts()` method
- [ ] Add relationship: `hasMany(Office::class)`
- [ ] Update factory with realistic data
- [ ] Create model: `php artisan make:model Office -f`
- [ ] Add fillable attributes
- [ ] Define `casts()` method
- [ ] Add relationship: `belongsTo(Organization::class)`
- [ ] Update factory with realistic address data

### Implementation - Backend (Organization)
- [ ] Create Data: `php artisan make:data UpdateOrganizationData`
- [ ] Add validation attributes (name required, vat/ssn/phone formats)
- [ ] Create Action: `php artisan make:action UpdateOrganization`
- [ ] Implement organization update logic
- [ ] Create controller: `php artisan make:controller AdminOrganizationController`
- [ ] Add `edit()` method - show organization form
- [ ] Add `update()` method - save organization
- [ ] Create routes:
  - `GET /admin/settings/organization`
  - `PUT /admin/settings/organization`
- [ ] Add middleware: `can:organizations.edit`

### Implementation - Backend (Offices)
- [ ] Create Data: `php artisan make:data CreateOfficeData`
- [ ] Add validation for office fields (name, address, phone required)
- [ ] Create Data: `php artisan make:data UpdateOfficeData`
- [ ] Add validation for office updates
- [ ] Create Action: `php artisan make:action CreateOffice`
- [ ] Implement office creation with organization relationship
- [ ] Create Action: `php artisan make:action UpdateOffice`
- [ ] Implement office update logic
- [ ] Create Action: `php artisan make:action DeleteOffice`
- [ ] Implement office deletion
- [ ] Create controller: `php artisan make:controller AdminOfficeController`
- [ ] Add `store()` method - create office
- [ ] Add `update()` method - update office
- [ ] Add `destroy()` method - delete office
- [ ] Create routes:
  - `POST /admin/settings/organization/offices`
  - `PUT /admin/settings/organization/offices/{office}`
  - `DELETE /admin/settings/organization/offices/{office}`
- [ ] Add middleware: `can:organizations.edit`

### Implementation - Frontend
- [ ] Create page: `resources/js/pages/admin/settings/organization.tsx`
- [ ] Use AdminSettingsLayout wrapper
- [ ] Create organization form section:
  - Name input (required)
  - VAT number input
  - SSN input
  - Phone input
  - Country select
- [ ] Create offices management section:
  - Offices list/table
  - Add office button
  - Edit office inline or modal
  - Delete office with confirmation
- [ ] Implement office form fields:
  - Name input (required)
  - Address line 1 (required)
  - Address line 2 (optional)
  - City (required)
  - State/Province
  - Postal code (required)
  - Country select (required)
  - Phone input
- [ ] Use Inertia `useForm` for all forms
- [ ] Add validation error display
- [ ] Add success/error notifications
- [ ] Style with Tailwind v4
- [ ] Make responsive
- [ ] Add dark mode support

### Verification
- [ ] All unit tests pass
- [ ] All feature tests pass
- [ ] All browser tests pass
- [ ] Organization updates work
- [ ] Office CRUD works (create, update, delete)
- [ ] People Manager can access
- [ ] Owner can access
- [ ] Employee gets 403
- [ ] Form validation works
- [ ] No TypeScript errors
- [ ] Build succeeds
- [ ] Run all tests: `php artisan test`
- [ ] Run Pint: `vendor/bin/pint --dirty`
- [ ] No PHPStan errors

### PR & Merge
- [ ] Commit changes
- [ ] Push branch
- [ ] Create PR with screenshots
- [ ] Merge to main
- [ ] Update this file: Mark Phase 4 as ✅

---

## Phase 5: System Settings (Owner Only)

**Branch:** `feature/system-settings`
**Status:** Not Started
**Dependencies:** Phase 3, Phase 4 completed

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/system-settings`

### Unit Tests (TDD - Write First)
- [ ] Create test: `tests/Unit/Settings/GeneralSettingsTest.php`
- [ ] Test: GeneralSettings class has correct properties
- [ ] Test: Settings can be retrieved and updated
- [ ] Create test: `tests/Unit/Settings/MailSettingsTest.php`
- [ ] Test: MailSettings class has correct properties
- [ ] Create test: `tests/Unit/Settings/SecuritySettingsTest.php`
- [ ] Test: SecuritySettings class has correct properties
- [ ] Create test: `tests/Unit/Actions/UpdateSystemSettingsTest.php`
- [ ] Test: Action updates system settings correctly
- [ ] Test: Action validates settings before saving
- [ ] Create test: `tests/Unit/Data/UpdateSystemSettingsDataTest.php`
- [ ] Test: Data object validates required fields
- [ ] Test: Data object validates email format
- [ ] Test: Data object validates password policy fields

### Feature Tests (TDD - Write First)
- [ ] Create test: `tests/Feature/Controllers/AdminSystemSettingsControllerTest.php`
- [ ] Test: Owner can access system settings page
- [ ] Test: People manager cannot access (403)
- [ ] Test: Employee cannot access (403)
- [ ] Test: System settings update successfully
- [ ] Test: Validation errors return correctly
- [ ] Test: Settings persist to database

### Browser Tests (TDD - Write First)
- [ ] Create test: `tests/Browser/SystemSettingsTest.php`
- [ ] Test: System settings page renders for owner
- [ ] Test: Page forbidden for people_manager (403)
- [ ] Test: Page forbidden for employee (403)
- [ ] Test: Settings form pre-populated with current values
- [ ] Test: Form submission works
- [ ] Test: Success message displays
- [ ] Test: Validation errors display inline
- [ ] Test: All form sections render (User Mgmt, Email, Security, Auth)

### Implementation - Settings Classes (Spatie Laravel Settings)
- [ ] Install package: `composer require spatie/laravel-settings`
- [ ] Publish migration: `php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"`
- [ ] Run migration: `php artisan migrate`
- [ ] Create settings class: `app/Settings/GeneralSettings.php`
  - Add properties: `defaultRole`, `registrationEnabled`, `emailVerificationRequired`
  - Add proper types and defaults
- [ ] Create settings class: `app/Settings/MailSettings.php`
  - Add properties: `fromEmail`, `fromName`, `notificationPreferences`
  - Add validation in properties
- [ ] Create settings class: `app/Settings/SecuritySettings.php`
  - Add properties: `passwordMinLength`, `passwordRequirements`, `twoFactorRequired`, `sessionTimeout`
- [ ] Register settings in service provider if needed

### Implementation - Backend
- [ ] Create Data object: `php artisan make:data UpdateSystemSettingsData`
- [ ] Add validation attributes for all settings fields
- [ ] Create Action: `php artisan make:action UpdateSystemSettings`
- [ ] Inject settings classes: `GeneralSettings`, `MailSettings`, `SecuritySettings`
- [ ] Implement settings update logic using Spatie Settings
- [ ] Create controller: `php artisan make:controller AdminSystemSettingsController`
- [ ] Add `edit()` method - load and return current settings
- [ ] Add `update()` method - save settings via action
- [ ] Create routes in `routes/web.php`:
  - `GET /admin/settings/system`
  - `PUT /admin/settings/system`
- [ ] Add middleware: `can:settings.manage`

### Implementation - Frontend
- [ ] Create page: `resources/js/pages/admin/settings/system.tsx`
- [ ] Use AdminSettingsLayout wrapper
- [ ] Create form sections:
  - **User Management:** default role dropdown, registration toggle, email verification toggle
  - **Email Settings:** from email, from name, SMTP config, notification checkboxes
  - **Security:** password min length, password requirements, 2FA toggle, session timeout
  - **Authentication:** email/password toggle, OAuth placeholders
- [ ] Use Inertia `useForm` helper
- [ ] Add form validation
- [ ] Add submit handler
- [ ] Add success/error toast notifications
- [ ] Style with Tailwind v4
- [ ] Make responsive
- [ ] Add dark mode support

### Verification
- [ ] All unit tests pass
- [ ] All feature tests pass
- [ ] All browser tests pass
- [ ] System settings save correctly
- [ ] Form validation works
- [ ] Owner can access
- [ ] People Manager gets 403
- [ ] Employee gets 403
- [ ] No TypeScript errors
- [ ] Build succeeds
- [ ] Run all tests: `php artisan test`
- [ ] Run Pint: `vendor/bin/pint --dirty`
- [ ] No PHPStan errors

### PR & Merge
- [ ] Commit changes
- [ ] Push branch
- [ ] Create PR with form screenshots
- [ ] Merge to main
- [ ] Update this file: Mark Phase 5 as ✅

---

## Global Checklist (All Phases)

### Before Each PR
- [ ] All tests passing: `php artisan test`
- [ ] Code formatted: `vendor/bin/pint --dirty`
- [ ] No static analysis errors: `vendor/bin/phpstan analyse`
- [ ] No TypeScript errors: `npm run typecheck`
- [ ] Build succeeds: `npm run build`
- [ ] Browser tests pass (no JS errors, no console errors)

### Code Quality Standards
- [ ] All variables type hinted (PHPDoc)
- [ ] All models use `Model::query()`
- [ ] All tests use `createQuietly()`
- [ ] Migration records use `fresh()`
- [ ] Expect methods chained
- [ ] All classes imported (no inline qualified names)
- [ ] Methods chained on new lines
- [ ] No `down()` methods in migrations
- [ ] No default values in migrations
- [ ] Actions use `handle()` method
- [ ] Controllers follow single responsibility

### Documentation
- [ ] Update requirements.md if scope changes
- [ ] Update plan.md if architecture changes
- [ ] Update tasks.md after each completed task
- [ ] Add comments for complex logic
- [ ] Update CLAUDE.md if new conventions established

---

## Notes & Decisions

### Decision Log
- **2025-10-25:** Admin route prefix `/admin/*` instead of role-based dashboard
- **2025-10-25:** Settings use key-value table for flexibility
- **2025-10-25:** TDD approach for all features
- **2025-10-25:** One organization per system (current scope)
- **2025-10-25:** Organizations section only visible to Owner

### Questions & Answers
- **Q:** Should admin dashboard be separate route or shared?
  - **A:** Separate route `/admin/dashboard`

- **Q:** What widgets on admin dashboard?
  - **A:** User statistics, Organization overview, Quick actions

- **Q:** What in general settings?
  - **A:** User management, Email settings, System configuration, Security settings

- **Q:** TDD or tests after?
  - **A:** TDD (tests first)

### Open Questions
- None currently

---

## Progress Tracking

### Phase Status Legend
- ⏳ Not Started
- 🔄 In Progress
- ✅ Completed
- ❌ Blocked

### Current Status
- Phase 1: ⏳ Not Started
- Phase 2: ⏳ Not Started
- Phase 3: ⏳ Not Started
- Phase 4: ⏳ Not Started
- Phase 5: ⏳ Not Started

### Overall Progress
**0/5 phases completed (0%)**

---

## Next Session Actions

When resuming work:
1. Check this file for current phase status
2. Review requirements.md for context
3. Review plan.md for architecture decisions
4. Continue with next unchecked task
5. Update checkboxes as you complete tasks
6. Update phase status when phase completes
