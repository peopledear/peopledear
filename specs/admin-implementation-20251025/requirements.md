# Admin Features - Requirements Document

**Project:** PeopleDear
**Created:** 2025-10-25
**Last Updated:** 2025-10-25

---

## User Roles

### People Manager Role
- HR administrator with employee management capabilities
- Can access admin dashboard and general settings
- Cannot access organization-level settings

### Owner Role
- Full system administrator
- Has all People Manager permissions
- Additionally can manage organization settings (company info, offices)

---

## Feature Requirements

### 1. Admin Layout

**Description:** Create a dedicated admin layout for People Manager and Owner roles with admin-specific navigation.

**User Story:** As a People Manager or Owner, I need a dedicated admin interface with easy access to admin features so I can efficiently manage the system.

**Requirements:**
- Separate layout component for admin pages
- Navigation sidebar or header with admin menu items
- Role-based navigation visibility
- Responsive design (mobile, tablet, desktop)
- Dark mode support
- Active page highlighting
- Breadcrumbs for navigation context

**Access Control:**
- Accessible by: People Manager, Owner
- Required permissions: Any admin permission

---

### 2. Admin Dashboard

**Description:** Admin-specific dashboard showing system statistics and quick actions.

**User Story:** As a People Manager or Owner, I need an overview dashboard showing key metrics and quick access to common tasks.

**Route:** `/admin/dashboard`

**Requirements:**
- **User Statistics Widget:**
  - Total users count
  - Active users count
  - Recent registrations (last 7/30 days)

- **Organization Overview Widget:**
  - Number of organizations
  - Growth metrics

- **Quick Actions Widget:**
  - Create new user button
  - Manage roles button
  - View reports button
  - Other frequently used actions

**Design:**
- Follow existing dashboard design pattern (`resources/js/pages/dashboard.tsx`)
- Grid layout: 3 cards on desktop, stacked on mobile
- Use PlaceholderPattern for visual consistency initially
- Real statistics (not hardcoded)

**Access Control:**
- Accessible by: People Manager, Owner
- Required permissions: `employees.view`

---

### 3. Admin Settings Layout

**Description:** Settings-specific layout with sidebar navigation for different settings sections.

**User Story:** As a People Manager or Owner, I need organized access to different system settings so I can configure the system efficiently.

**Requirements:**
- Settings sidebar navigation with sections
- Role-based section visibility
- Active section highlighting
- Responsive sidebar (collapsible on mobile)

**Navigation Sections:**
- **Organization** (People Manager & Owner) - Company info, offices, addresses
- **System** (Owner only - conditional) - User management, email, security, auth

**Access Control:**
- Accessible by: People Manager (organization section only), Owner (all sections)
- Section visibility based on user permissions:
  - People Manager sees: Organization section only
  - Owner sees: Organization + System sections

---

### 4. Organization Settings (People Manager & Owner Access)

**Description:** Organization-level configuration including company information and office locations.

**User Story:** As a People Manager or Owner, I need to manage organization details and office locations so company information is accurate across the system.

**Route:**
- GET `/admin/settings/organization` - Show organization form
- PUT `/admin/settings/organization` - Update organization
- POST `/admin/settings/organization/offices` - Create office
- PUT `/admin/settings/organization/offices/{office}` - Update office
- DELETE `/admin/settings/organization/offices/{office}` - Delete office

**Settings Sections:**

#### Organization Information
- Organization name (required)
- VAT number
- SSN/Tax ID
- Phone number
- Country

#### Offices Management
- Office name (required)
- Address line 1 (required)
- Address line 2 (optional)
- City (required)
- State/Province
- Postal code (required)
- Country (required)
- Phone number

**Features:**
- List all offices with edit/delete actions
- Add new office button
- Inline editing or modal-based CRUD
- Delete confirmation dialog
- Address validation (postal code format per country)

**Technical Details:**
- `organizations` table for company info
- `offices` table with `organization_id` foreign key
- One organization per system (current scope)
- Multiple offices per organization
- Soft deletes for offices (optional)

**Access Control:**
- Accessible by: People Manager, Owner
- Required permissions: `organizations.edit`

---

### 5. System Settings (Owner Only)

**Description:** System-level configuration for security, authentication, user management, and email settings.

**User Story:** As an Owner, I need to configure system-level settings like security policies, authentication methods, and email configuration so I can control how the entire system operates.

**Route:**
- GET `/admin/settings/system` - Show system settings form
- PUT `/admin/settings/system` - Update system settings

**Settings Sections:**

#### User Management Settings
- Default role for new users (dropdown)
- Registration enabled/disabled toggle
- Email verification required toggle

#### Email Settings
- From email address (required)
- From name (required)
- SMTP configuration
- Notification preferences (checkboxes for different notification types)

#### Security Settings
- Password minimum length
- Password requires uppercase/lowercase/numbers/symbols
- Two-factor authentication required toggle
- Session timeout duration

#### Authentication Methods
- Email/password login enabled
- OAuth providers configuration (future)
- SSO settings (future)

**Technical Details:**
- Settings managed using **Spatie Laravel Settings** package
- Type-safe settings classes: `GeneralSettings`, `MailSettings`, `SecuritySettings`
- Settings cached automatically for performance
- Form validation for email format, password policies
- Success/error feedback
- Sensitive settings encrypted

**Access Control:**
- Accessible by: Owner only
- Required permissions: `settings.manage`

---

## Authorization Matrix

| Feature | People Manager | Owner | Permission Required |
|---------|----------------|-------|---------------------|
| Admin Layout | ✓ | ✓ | Any admin permission |
| Admin Dashboard | ✓ | ✓ | `employees.view` |
| Settings Layout | ✓ | ✓ | Any setting permission |
| Organization Settings | ✓ | ✓ | `organizations.edit` |
| System Settings | ✗ | ✓ | `settings.manage` |
| User Management Settings | ✗ | ✓ | `settings.manage` |
| Email Settings | ✗ | ✓ | `settings.manage` |
| Security Settings | ✗ | ✓ | `settings.manage` |

---

## Design Guidelines

### Visual Consistency
- Match existing dashboard design patterns
- Use PlaceholderPattern component for empty states
- Follow Tailwind v4 conventions
- Support dark mode throughout
- Responsive breakpoints: mobile, tablet, desktop

### UI Components
- Use existing shadcn/ui components from `@/components/ui/`
  - Button, Card, Input, Label, Select, Dialog, Sheet, Dropdown, Badge, Avatar, Skeleton
- Check `resources/js/components/ui/` for available components before creating new ones
- Consistent spacing with gap utilities
- Proper form validation feedback
- Toast notifications for success/error states (using shadcn toast if available)

### Navigation
- Clear breadcrumbs on all pages
- Active state highlighting
- Mobile-friendly navigation (hamburger or drawer)
- Logical grouping of related features

---

## Technical Constraints

### Database
- Follow migration guidelines (no `down()` methods, no defaults)
- Use `foreignIdFor()` for relationships
- No cascade constraints
- PostgreSQL compatibility (no `after()` in migrations)

### Backend
- Use Action pattern for business logic
- Use Data objects for validation
- Use `Model::query()` for all queries
- Type hint all variables
- Follow existing controller patterns

### Frontend
- React 18 with Inertia.js v2 (@inertiajs/react)
- TypeScript 5 with proper typing for all components
- Tailwind v4 for styling
- shadcn/ui components from `@/components/ui/`
- Form handling with Inertia's `useForm` helper
- Form validation with Laravel Data objects (backend)
- Optimistic UI updates where appropriate

### Testing
- TDD approach (tests first)
- Comprehensive coverage: browser, feature, unit tests
- Type hint all test variables
- Use `createQuietly()` in tests
- Use `fresh()` for migration-seeded records
- All tests must pass before PR

---

## Success Criteria

### Phase Completion
Each phase is complete when:
- All tasks checked off
- All tests passing (green CI)
- Code formatted with Pint
- PR created and approved
- Merged to main

### Overall Success
Implementation is successful when:
- All 5 phases completed
- People Manager can access admin features (except org settings)
- Owner can access all admin features including org settings
- All authorization checks working
- Full test coverage (100% of features tested)
- Clean, maintainable code following project conventions
- Documentation complete
