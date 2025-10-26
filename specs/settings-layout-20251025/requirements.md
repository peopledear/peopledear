# Settings Layout - Requirements Document

**Project:** PeopleDear
**Created:** 2025-10-25
**Last Updated:** 2025-10-25

---

## Overview

Create a unified settings layout with sidebar navigation for both user settings and organization settings, providing a consistent user experience across different settings sections.

---

## Current State

### User Settings (Already Implemented)
Currently, user settings pages exist but lack a unified layout:
- Profile settings: `/settings/profile`
- Password settings: `/settings/password`
- Appearance settings: `/settings/appearance`
- Two-factor authentication: `/settings/two-factor`

These pages currently redirect from `/settings` and don't have a cohesive navigation structure.

### Organization Settings (Already Implemented)
Organization settings exist at:
- Organization general settings: `/org/settings` (AdminLayout wrapper)

This page exists but lacks sidebar navigation for future organization-level settings sections.

---

## Feature Requirements

### 1. User Settings Layout

**Description:** Create a unified settings layout for user-specific settings with sidebar navigation and card-based content organization.

**User Story:** As any authenticated user, I need a consistent settings interface with easy navigation between different user settings sections, with each section's content organized in clear, separate cards.

**Route:** `/settings/*`

**Requirements:**
- Settings layout component with sidebar navigation
- Navigation sections:
  - **Profile** - User profile information
  - **Password** - Password management
  - **Appearance** - Theme/appearance preferences
  - **Security** - Two-factor authentication and security settings
- Active section highlighting
- Responsive design (sidebar collapses on mobile)
- Dark mode support
- Breadcrumbs showing current section
- Smooth transitions between sections

**Content Organization:**
- **Each settings page must use shadcn/ui Card components** to organize content
- **Multiple cards per page for different functional sections**
  - Example: Profile page has "Profile Information" card + "Delete Account" card
  - Example: Security page has "Two-Factor Authentication" card + "Active Sessions" card
- **Card structure:**
  - Card header with title and optional description
  - Card content with form fields or information
  - Card footer with action buttons (if needed)
- **Destructive actions in separate "Danger" cards** with red/destructive styling

**Access Control:**
- Accessible by: All authenticated users
- Required permissions: None (authenticated only)

**Design:**
- Sidebar on left (desktop) or collapsible drawer (mobile)
- Main content area on right with card-based layout
- Each card has proper spacing, borders, and shadows
- Cards stack vertically with consistent gap spacing
- Use shadcn/ui Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter components
- Match Laravel Forge-style settings design pattern (reference image)

---

### 2. Organization Settings Layout

**Description:** Create a unified settings layout for organization-level settings with sidebar navigation and card-based content organization, accessible by People Manager and Owner roles.

**User Story:** As a People Manager or Owner, I need organized access to different organization settings sections so I can efficiently manage organization-level configuration, with each section's content organized in clear, separate cards.

**Route:** `/org/settings/*`

**Requirements:**
- Settings layout component with sidebar navigation
- Navigation sections (all accessible by People Manager and Owner):
  - **General** - Organization name, VAT, SSN, phone
  - **Offices** (Future) - Office locations and addresses
  - **Billing** (Future) - Billing information and payment methods
  - **Team** (Future) - Team members and permissions
- Active section highlighting
- Responsive design (sidebar collapses on mobile)
- Dark mode support
- Breadcrumbs showing current section
- Smooth transitions between sections

**Content Organization:**
- **Each settings page must use shadcn/ui Card components** to organize content
- **Organization General page example:**
  - "General" card - Organization name, VAT, SSN, phone fields
  - "Danger" card - Delete organization or other destructive actions (if applicable)
- **Card structure:**
  - Card header with title and optional description
  - Card content with form fields or information
  - Card footer with action buttons (if needed)
- **Destructive actions in separate "Danger" cards** with red/destructive styling

**Access Control:**
- Accessible by: People Manager, Owner
- Required permissions: `organizations.edit`
- Role-based section visibility (all sections visible to both roles for now)

**Design:**
- Similar visual structure to user settings layout
- Sidebar on left (desktop) or collapsible drawer (mobile)
- Main content area on right with card-based layout
- Each card has proper spacing, borders, and shadows
- Cards stack vertically with consistent gap spacing
- Use shadcn/ui Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter components
- Match Laravel Forge-style settings design pattern (reference image: `specs/settings-layout-20251025/img.png`)

---

### 3. Migrate Existing Pages to New Layouts

**Description:** Update existing settings pages to use the new unified layouts.

**Requirements:**

#### User Settings Pages
- Update `/settings/profile` to use UserSettingsLayout
- Update `/settings/password` to use UserSettingsLayout
- Update `/settings/appearance` to use UserSettingsLayout
- Update `/settings/two-factor` to use UserSettingsLayout
- Update redirect from `/settings` to show profile page with layout

#### Organization Settings Pages
- Update `/org/settings` (organization general) to use OrgSettingsLayout
- Ensure proper breadcrumbs and active state highlighting

**Testing:**
- Verify all existing functionality still works
- Verify navigation between sections works
- Verify mobile responsive behavior
- Verify dark mode support
- Verify authorization still works correctly

---

## Technical Constraints

### Frontend
- React 18 with TypeScript 5
- Inertia.js v2 for page transitions
- Tailwind v4 for styling
- shadcn/ui components for UI primitives
- Mobile-first responsive design
- Dark mode support throughout

### Layout Architecture
- Two separate layout components:
  - `resources/js/layouts/user-settings-layout.tsx` - User settings wrapper
  - `resources/js/layouts/org-settings-layout.tsx` - Organization settings wrapper
- Layouts should be composable and reusable
- Active route detection for highlighting current section
- Proper TypeScript typing for all props

### Routing
- User settings: `/settings/*` prefix
- Organization settings: `/org/settings/*` prefix
- Maintain existing route names for backward compatibility
- Use Laravel Wayfinder for type-safe routing

### Testing
- Browser tests for navigation flow
- Browser tests for responsive behavior
- Browser tests for authorization checks
- Feature tests for route accessibility
- All tests must pass before PR

---

## Navigation Structure

### User Settings Sidebar
```
Settings
├── Profile
├── Password
├── Appearance
└── Security (Two-Factor)
```

### Organization Settings Sidebar
```
Organization Settings
├── General
├── Offices (Future - gray/disabled)
├── Billing (Future - gray/disabled)
└── Team (Future - gray/disabled)
```

---

## Success Criteria

### Implementation Complete When:
- User settings layout implemented and working
- Organization settings layout implemented and working
- All existing settings pages migrated to new layouts
- Navigation between sections works smoothly
- Mobile responsive behavior works correctly
- Dark mode support works throughout
- All authorization checks still working
- All tests passing (browser, feature)
- Code formatted with Pint
- No TypeScript errors
- No console errors in browser

### User Experience Success:
- Settings navigation is intuitive and easy to use
- Active section is clearly highlighted
- Mobile navigation works smoothly
- Transitions between sections are smooth
- Layout is consistent with rest of application
- Dark mode looks good and is consistent

---

## Design Guidelines

### Visual Consistency
- Match existing application design patterns
- Use consistent spacing (gap utilities)
- Follow Tailwind v4 conventions
- Support dark mode throughout
- Responsive breakpoints: mobile (< 768px), tablet (768px-1024px), desktop (> 1024px)

### UI Components
- Use shadcn/ui components from `@/components/ui/`:
  - Separator for dividers
  - Button for navigation items
  - Sheet for mobile sidebar
  - Card for content containers
- Reuse existing components where possible
- Create new components only when necessary

### Navigation Behavior
- Highlight active section clearly
- Show hover states on navigation items
- Smooth transitions between pages (Inertia.js)
- Maintain scroll position when navigating sections
- Mobile: collapsible sidebar/drawer
- Desktop: persistent sidebar

### Accessibility
- Proper ARIA labels for navigation
- Keyboard navigation support
- Focus management on page transitions
- Screen reader friendly

---

## Out of Scope

The following are explicitly NOT included in this spec:
- Implementation of future sections (Offices, Billing, Team)
- Adding new settings fields or functionality
- Changing validation or business logic
- Database migrations or backend changes
- System-wide settings (those remain separate if they exist)

---

## Dependencies

### Existing Code
- AdminLayout already exists and is used
- User settings pages already exist
- Organization settings page already exists
- Authorization middleware already works

### New Components Needed
- UserSettingsLayout component
- OrgSettingsLayout component
- SettingsSidebar component (shared/reusable)

---

## Migration Path

### Phase 1: Create Layouts
1. Create UserSettingsLayout component
2. Create OrgSettingsLayout component
3. Create shared SettingsSidebar component (if beneficial)

### Phase 2: Migrate User Settings
1. Update profile page to use UserSettingsLayout
2. Update password page to use UserSettingsLayout
3. Update appearance page to use UserSettingsLayout
4. Update two-factor page to use UserSettingsLayout
5. Update `/settings` redirect to show profile with layout

### Phase 3: Migrate Organization Settings
1. Update organization general page to use OrgSettingsLayout
2. Ensure breadcrumbs and navigation work correctly

### Phase 4: Testing & Refinement
1. Write browser tests for navigation
2. Write browser tests for responsive behavior
3. Test authorization on all pages
4. Test dark mode throughout
5. Refine styling and UX
6. Fix any issues found

---

## Risk Mitigation

### Technical Risks
- **Risk:** Breaking existing functionality during migration
  - **Mitigation:** Comprehensive testing, incremental migration

- **Risk:** Mobile navigation complexity
  - **Mitigation:** Use shadcn Sheet component, follow mobile-first approach

- **Risk:** Inertia.js state management with sidebar
  - **Mitigation:** Use Inertia's built-in active link detection

### User Experience Risks
- **Risk:** Users confused by new navigation
  - **Mitigation:** Keep navigation simple and intuitive, match common patterns

- **Risk:** Performance issues with layout re-renders
  - **Mitigation:** Proper React memoization, Inertia persistent layouts

---

## Questions & Decisions

### Should user settings and org settings share a component?
**Decision:** Create separate but similar layouts. They have different navigation items and may diverge in the future. Code can be shared through smaller reusable components if needed.

### Should sidebar be collapsible on desktop?
**Decision:** No, sidebar is persistent on desktop. Only collapses on mobile (< 768px).

### Should we use Inertia persistent layouts?
**Decision:** Yes, use Inertia's persistent layout feature to avoid re-rendering the sidebar on navigation.

### What about breadcrumbs?
**Decision:** Include breadcrumbs in layout, showing "Settings > [Section Name]" structure.

---

## Timeline Estimate

- **Phase 1 (Create Layouts):** 3-4 hours
- **Phase 2 (Migrate User Settings):** 2-3 hours
- **Phase 3 (Migrate Org Settings):** 1-2 hours
- **Phase 4 (Testing & Refinement):** 2-3 hours

**Total Estimated:** 8-12 hours

---

## Appendix

### Related Files
- Existing user settings pages: `resources/js/pages/user-*/`
- Existing org settings page: `resources/js/pages/org-settings-general/edit.tsx`
- Existing admin layout: `resources/js/layouts/admin-layout.tsx`
- Routes: `routes/web.php`

### Reference Documentation
- Inertia.js persistent layouts: Use for avoiding sidebar re-renders
- shadcn/ui Sheet component: For mobile drawer
- Tailwind v4 responsive utilities: For mobile/desktop layouts