# Settings Layout - Implementation Plan

**Project:** PeopleDear
**Spec:** Settings Layout Implementation
**Created:** 2025-10-25
**Strategy:** Test-Driven Development (TDD)

---

## High-Level Overview

Create unified settings layouts for both user settings and organization settings, then migrate existing settings pages to use these new layouts. This provides a consistent, intuitive settings experience with sidebar navigation.

### Implementation Approach
1. **User Settings Layout** - Create layout with sidebar for user settings
2. **Organization Settings Layout** - Create layout with sidebar for organization settings
3. **Migration** - Update existing pages to use new layouts
4. **Testing** - Comprehensive browser and feature tests

---

## Architecture Decisions

### Frontend Architecture
- **Framework:** React with Inertia.js v2
- **Layout Strategy:** Inertia persistent layouts to avoid sidebar re-renders
- **Layouts:**
  - `user-settings-layout.tsx` - User settings wrapper with sidebar
  - `org-settings-layout.tsx` - Organization settings wrapper with sidebar
- **Shared Components:** Potentially extract shared sidebar logic if beneficial
- **Styling:** Tailwind v4 with dark mode support
- **Type Safety:** Full TypeScript coverage

### Component Structure

```
layouts/
├── admin-layout.tsx (existing)
├── user-settings-layout.tsx (new)
└── org-settings-layout.tsx (new)

components/
├── ui/ (existing shadcn components)
└── settings-sidebar.tsx (optional shared component)
```

### Routing Structure

#### User Settings Routes (No Changes)
```
GET  /settings           -> redirect to /settings/profile
GET  /settings/profile   -> UserSettingsLayout
GET  /settings/password  -> UserSettingsLayout
GET  /settings/appearance -> UserSettingsLayout
GET  /settings/two-factor -> UserSettingsLayout
```

#### Organization Settings Routes (No Changes)
```
GET  /org/settings       -> OrgSettingsLayout (organization general)
PUT  /org/settings/organization -> existing update route
```

### Layout Props Interface

```typescript
// UserSettingsLayout props
interface UserSettingsLayoutProps {
    children: ReactNode;
    activeSection?: 'profile' | 'password' | 'appearance' | 'security';
}

// OrgSettingsLayout props
interface OrgSettingsLayoutProps {
    children: ReactNode;
    activeSection?: 'general' | 'offices' | 'billing' | 'team';
}
```

---

## Testing Strategy (TDD Approach)

### Test Types

#### Browser Tests (`tests/Browser/`)
- User settings navigation flow
- Organization settings navigation flow
- Mobile responsive behavior (sidebar collapse)
- Active section highlighting
- Dark mode support
- Authorization checks

#### Feature Tests (`tests/Feature/`)
- Route accessibility for different roles
- Authorization middleware working
- Redirects working correctly

### TDD Workflow
```
1. Write failing test (Red)
2. Implement minimal code to pass (Green)
3. Refactor for quality (Refactor)
4. Repeat
```

---

## Implementation Phases

### Phase 1: User Settings Layout

**Complexity:** Medium
**Estimated Effort:** 3-4 hours
**Dependencies:** None

**Goal:** Create reusable user settings layout with sidebar navigation

**Key Files:**
- `resources/js/layouts/user-settings-layout.tsx`
- `tests/Browser/UserSettingsLayoutTest.php`

**Tasks:**
1. Create layout component with sidebar
2. Add navigation sections (Profile, Password, Appearance, Security)
3. Implement active section highlighting
4. Add responsive behavior (mobile drawer)
5. Add dark mode support
6. Write browser tests
7. Verify all tests pass

**Success Criteria:**
- Layout renders correctly
- Navigation works smoothly
- Mobile responsive behavior works
- Active highlighting works
- Tests passing

---

### Phase 2: Migrate User Settings Pages

**Complexity:** Low
**Estimated Effort:** 2-3 hours
**Dependencies:** Phase 1

**Goal:** Update existing user settings pages to use new layout

**Key Files:**
- `resources/js/pages/user-profile/edit.tsx`
- `resources/js/pages/user-password/edit.tsx`
- `resources/js/pages/appearance/update.tsx`
- `resources/js/pages/user-two-factor-authentication/show.tsx`

**Tasks:**
1. Update profile page to use UserSettingsLayout
2. Update password page to use UserSettingsLayout
3. Update appearance page to use UserSettingsLayout
4. Update two-factor page to use UserSettingsLayout
5. Test all pages still work correctly
6. Verify navigation between sections
7. Write/update browser tests

**Success Criteria:**
- All pages use new layout
- All existing functionality works
- Navigation between sections works
- Tests passing

---

### Phase 3: Organization Settings Layout

**Complexity:** Medium
**Estimated Effort:** 2-3 hours
**Dependencies:** Phase 1 (for consistency)

**Goal:** Create organization settings layout with sidebar navigation

**Key Files:**
- `resources/js/layouts/org-settings-layout.tsx`
- `tests/Browser/OrgSettingsLayoutTest.php`

**Tasks:**
1. Create layout component with sidebar
2. Add navigation sections (General, plus future placeholders)
3. Implement active section highlighting
4. Add responsive behavior (mobile drawer)
5. Add dark mode support
6. Implement authorization checks (People Manager & Owner only)
7. Write browser tests
8. Verify all tests pass

**Success Criteria:**
- Layout renders correctly for authorized users
- Navigation works smoothly
- Mobile responsive behavior works
- Only People Manager and Owner can access
- Tests passing

---

### Phase 4: Migrate Organization Settings Page

**Complexity:** Low
**Estimated Effort:** 1-2 hours
**Dependencies:** Phase 3

**Goal:** Update organization general settings page to use new layout

**Key Files:**
- `resources/js/pages/org-settings-general/edit.tsx`

**Tasks:**
1. Update organization general page to use OrgSettingsLayout
2. Ensure proper active section highlighting
3. Test page still works correctly
4. Verify authorization still works
5. Write/update browser tests

**Success Criteria:**
- Page uses new layout
- All existing functionality works
- Active highlighting works
- Authorization works
- Tests passing

---

### Phase 5: Testing & Refinement

**Complexity:** Medium
**Estimated Effort:** 2-3 hours
**Dependencies:** Phases 1-4

**Goal:** Comprehensive testing and UX refinement

**Tasks:**
1. Write comprehensive browser tests for all navigation flows
2. Test responsive behavior on different screen sizes
3. Test dark mode throughout
4. Test authorization on all pages
5. Refine styling and spacing
6. Fix any UX issues found
7. Ensure all tests pass
8. Run Pint and fix formatting
9. Verify no TypeScript errors

**Success Criteria:**
- All browser tests passing
- All feature tests passing
- Responsive design works perfectly
- Dark mode works throughout
- No console errors
- Code formatted
- Ready for PR

---

## Git Workflow

### Branch Strategy
```bash
# Start implementation
git fetch
git pull origin main
git checkout -b feature/settings-layout

# TDD Development
# - Write tests first (failing)
# - Implement features
# - Make tests pass
# - Refactor

# Before PR
php artisan test           # Must pass
vendor/bin/pint --dirty   # Format code
npm run build             # Verify build

# Commit
git add .
git commit -m "feat: add unified settings layouts with sidebar navigation"
git push origin feature/settings-layout

# Create PR with:
# - Summary of changes
# - Screenshots (before/after)
# - Test coverage summary
```

---

## Technology Stack

### Frontend
- **React 18** - UI library
- **Inertia.js v2 (@inertiajs/react)** - Server-driven SPA with persistent layouts
- **TypeScript 5** - Type safety
- **Tailwind v4** - Styling
- **shadcn/ui** - Component library (Sheet, Separator, Button, etc.)
- **Laravel Wayfinder** - Type-safe routing

### Testing
- **Pest 4** - Browser testing with Playwright
- **Pest** - Feature and unit tests

### Development Tools
- **Laravel Pint** - Code formatting
- **Vite 6** - Build tool
- **TypeScript** - Type checking

---

## Component Design

### User Settings Layout Structure

```tsx
<UserSettingsLayout activeSection="profile">
  <div className="flex h-screen">
    {/* Sidebar - Desktop */}
    <aside className="hidden md:flex w-64 border-r">
      <nav>
        <NavItem href="/settings/profile" active={activeSection === 'profile'}>
          Profile
        </NavItem>
        <NavItem href="/settings/password" active={activeSection === 'password'}>
          Password
        </NavItem>
        <NavItem href="/settings/appearance" active={activeSection === 'appearance'}>
          Appearance
        </NavItem>
        <NavItem href="/settings/two-factor" active={activeSection === 'security'}>
          Security
        </NavItem>
      </nav>
    </aside>

    {/* Mobile Drawer */}
    <Sheet>
      {/* Same navigation items */}
    </Sheet>

    {/* Main Content */}
    <main className="flex-1 overflow-y-auto">
      <div className="container max-w-4xl py-8">
        {/* Breadcrumbs */}
        <Breadcrumbs />

        {/* Page Content */}
        {children}
      </div>
    </main>
  </div>
</UserSettingsLayout>
```

### Organization Settings Layout Structure

Similar structure to UserSettingsLayout but with different navigation items:
- General
- Offices (disabled/gray for now)
- Billing (disabled/gray for now)
- Team (disabled/gray for now)

### Page Content Structure (Card-Based)

**IMPORTANT:** All settings page content MUST be organized using shadcn/ui Card components.

#### Example: Profile Settings Page
```tsx
// Profile page example with cards
export default function ProfileEdit() {
  return (
    <UserSettingsLayout activeSection="profile">
      <div className="space-y-6">
        {/* Profile Information Card */}
        <Card>
          <CardHeader>
            <CardTitle>Profile Information</CardTitle>
            <CardDescription>
              Update your account's profile information and email address.
            </CardDescription>
          </CardHeader>
          <CardContent>
            {/* Form fields here */}
            <form className="space-y-4">
              <Input name="name" label="Name" />
              <Input name="email" label="Email" />
            </form>
          </CardContent>
          <CardFooter>
            <Button type="submit">Save Changes</Button>
          </CardFooter>
        </Card>

        {/* Delete Account Card - Danger Zone */}
        <Card className="border-destructive">
          <CardHeader>
            <CardTitle className="text-destructive">Danger</CardTitle>
            <CardDescription>
              Permanently delete your account.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>
          </CardContent>
          <CardFooter>
            <Button variant="destructive">Delete Account</Button>
          </CardFooter>
        </Card>
      </div>
    </UserSettingsLayout>
  );
}
```

#### Example: Organization General Settings Page
```tsx
// Organization general page example with cards (matches reference image)
export default function OrgGeneralEdit() {
  return (
    <OrgSettingsLayout activeSection="general">
      <div className="space-y-6">
        {/* General Settings Card */}
        <Card>
          <CardHeader>
            <CardTitle>General</CardTitle>
            <CardDescription>
              General settings related to the organization.
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {/* Organization Name */}
            <div>
              <Label>Organization name</Label>
              <Input value="Between Dynamic" />
              <p className="text-sm text-muted-foreground">
                Your handle is francisco-barrento-npa. <Link>Change</Link>
              </p>
            </div>

            {/* Tags */}
            <div>
              <Label>Tags</Label>
              <Button variant="outline">Manage tags</Button>
              <p className="text-sm text-muted-foreground">
                Tags are used to help you organize and find your servers or sites.
              </p>
            </div>

            {/* Organization Avatar */}
            <div>
              <Label>Organization avatar</Label>
              <div className="flex items-center gap-4">
                <Avatar>BD</Avatar>
                <Button variant="outline">Upload image</Button>
              </div>
              <p className="text-sm text-muted-foreground">
                Add an image to identify your organization.
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Danger Card */}
        <Card className="border-destructive">
          <CardHeader>
            <CardTitle className="text-destructive">Danger</CardTitle>
            <CardDescription>
              Destructive settings that cannot be undone.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div>
              <h4 className="font-medium">Delete organization</h4>
              <p className="text-sm text-muted-foreground">
                Deleting your organization will permanently delete all of its data...
              </p>
            </div>
          </CardContent>
          <CardFooter>
            <Button variant="destructive">Delete organization</Button>
          </CardFooter>
        </Card>
      </div>
    </OrgSettingsLayout>
  );
}
```

### Card Design Patterns

**Standard Card:**
- White background (light mode), dark background (dark mode)
- Border with subtle shadow
- Proper padding in CardContent
- CardHeader with title and optional description

**Danger Card:**
- Red border (`border-destructive`)
- Red title text (`text-destructive`)
- Destructive button variant
- Clear warning messaging

**Card Spacing:**
- Cards stack vertically with `space-y-6` gap
- Form fields inside cards use `space-y-4` gap
- CardContent sections use `space-y-6` gap

**Reference Image:** See `specs/settings-layout-20251025/img.png` for exact visual structure (Laravel Forge-style settings)

---

## Quality Assurance

### Code Quality Checks
- [ ] All tests passing (`php artisan test`)
- [ ] Code formatted (`vendor/bin/pint --dirty`)
- [ ] No TypeScript errors (`npm run build`)
- [ ] No console errors in browser tests
- [ ] Proper TypeScript typing throughout

### Test Coverage Requirements
- **Browser Tests:** All navigation flows, responsive behavior, authorization
- **Feature Tests:** Route accessibility, redirects
- **Visual Testing:** Screenshots in different states (light/dark, mobile/desktop)

### Code Review Checklist
- Follows project conventions (CLAUDE.md)
- Proper TypeScript typing
- Uses shadcn/ui components appropriately
- Responsive design works correctly
- Dark mode works correctly
- Accessibility considerations met
- No duplicate code

---

## Risk Mitigation

### Technical Risks
- **Risk:** Breaking existing settings pages during migration
  - **Mitigation:** Incremental migration, comprehensive testing after each page

- **Risk:** Inertia.js persistent layouts causing issues
  - **Mitigation:** Follow Inertia.js docs, test thoroughly

- **Risk:** Mobile navigation complexity
  - **Mitigation:** Use proven shadcn Sheet component

### UX Risks
- **Risk:** Users confused by new navigation
  - **Mitigation:** Keep it simple, follow common patterns

- **Risk:** Performance issues with sidebar re-renders
  - **Mitigation:** Use Inertia persistent layouts, React memoization

---

## Detailed Task Breakdown

### Phase 1: User Settings Layout (3-4 hours)

#### Browser Tests (TDD - Write First) [1 hour]
- [ ] Create test: `tests/Browser/UserSettingsLayoutTest.php`
- [ ] Test: Layout renders for authenticated user
- [ ] Test: All navigation sections visible
- [ ] Test: Active section highlighted correctly
- [ ] Test: Navigation between sections works
- [ ] Test: Mobile sidebar works (drawer opens/closes)
- [ ] Test: Dark mode works

#### Implementation [2 hours]
- [ ] Create component: `resources/js/layouts/user-settings-layout.tsx`
- [ ] Add TypeScript interfaces for props
- [ ] Implement desktop sidebar with navigation items
- [ ] Implement mobile drawer using shadcn Sheet
- [ ] Add active section highlighting logic
- [ ] Add breadcrumbs
- [ ] Style with Tailwind v4
- [ ] Add dark mode classes
- [ ] Make responsive (mobile/desktop)

#### Verification [1 hour]
- [ ] All browser tests pass
- [ ] Layout looks good in browser (manual check)
- [ ] Responsive behavior works correctly
- [ ] Dark mode works correctly
- [ ] No TypeScript errors
- [ ] Build succeeds

---

### Phase 2: Migrate User Settings Pages (2-3 hours)

#### Update Pages [1.5 hours]
- [ ] Update `user-profile/edit.tsx` to use UserSettingsLayout
- [ ] Update `user-password/edit.tsx` to use UserSettingsLayout
- [ ] Update `appearance/update.tsx` to use UserSettingsLayout
- [ ] Update `user-two-factor-authentication/show.tsx` to use UserSettingsLayout
- [ ] Pass correct `activeSection` prop to each page

#### Testing [1 hour]
- [ ] Test profile page works
- [ ] Test password page works
- [ ] Test appearance page works
- [ ] Test two-factor page works
- [ ] Test navigation between pages
- [ ] Write browser tests for navigation flow

#### Verification [0.5 hours]
- [ ] All pages render correctly
- [ ] All existing functionality works
- [ ] Navigation works smoothly
- [ ] Tests passing

---

### Phase 3: Organization Settings Layout (2-3 hours)

#### Browser Tests (TDD - Write First) [1 hour]
- [ ] Create test: `tests/Browser/OrgSettingsLayoutTest.php`
- [ ] Test: Layout renders for people_manager
- [ ] Test: Layout renders for owner
- [ ] Test: Layout forbidden for employee (403/redirect)
- [ ] Test: All navigation sections visible
- [ ] Test: Active section highlighted correctly
- [ ] Test: Mobile sidebar works
- [ ] Test: Dark mode works

#### Implementation [1.5 hours]
- [ ] Create component: `resources/js/layouts/org-settings-layout.tsx`
- [ ] Add TypeScript interfaces for props
- [ ] Implement desktop sidebar with navigation items
- [ ] Implement mobile drawer using shadcn Sheet
- [ ] Add active section highlighting logic
- [ ] Add breadcrumbs
- [ ] Style with Tailwind v4 (similar to user settings)
- [ ] Add dark mode classes
- [ ] Make responsive (mobile/desktop)
- [ ] Add disabled state for future sections

#### Verification [0.5 hours]
- [ ] All browser tests pass
- [ ] Layout looks good in browser
- [ ] Authorization works
- [ ] Tests passing

---

### Phase 4: Migrate Organization Settings Page (1-2 hours)

#### Update Page [0.5 hours]
- [ ] Update `org-settings-general/edit.tsx` to use OrgSettingsLayout
- [ ] Pass `activeSection="general"` prop
- [ ] Verify styling looks good

#### Testing [0.5 hours]
- [ ] Test page renders correctly
- [ ] Test existing functionality still works
- [ ] Test authorization still works
- [ ] Update browser tests if needed

#### Verification [0.5 hours]
- [ ] Page works correctly
- [ ] Navigation works
- [ ] Tests passing

---

### Phase 5: Testing & Refinement (2-3 hours)

#### Comprehensive Testing [1.5 hours]
- [ ] Write comprehensive browser test suite
- [ ] Test all user settings navigation flows
- [ ] Test all organization settings navigation flows
- [ ] Test responsive behavior on different screen sizes
- [ ] Test dark mode throughout all pages
- [ ] Test authorization on all pages
- [ ] Verify no console errors

#### Refinement [1 hour]
- [ ] Refine spacing and layout
- [ ] Fix any UX issues found
- [ ] Optimize performance if needed
- [ ] Add loading states if needed
- [ ] Polish animations/transitions

#### Final Checks [0.5 hours]
- [ ] Run all tests: `php artisan test`
- [ ] Format code: `vendor/bin/pint --dirty`
- [ ] Check TypeScript: `npm run build`
- [ ] Manual smoke test all pages
- [ ] Take screenshots for PR

---

## PR Checklist

### Before Creating PR
- [ ] All tests passing
- [ ] Code formatted with Pint
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] Manual testing completed
- [ ] Screenshots taken

### PR Description Should Include
- Summary of changes
- Before/after screenshots (light and dark mode)
- Mobile screenshots
- Test coverage summary
- Any UX considerations
- Migration notes (what changed for existing pages)

---

## Timeline Estimate

- **Phase 1:** 3-4 hours (User Settings Layout)
- **Phase 2:** 2-3 hours (Migrate User Settings)
- **Phase 3:** 2-3 hours (Org Settings Layout)
- **Phase 4:** 1-2 hours (Migrate Org Settings)
- **Phase 5:** 2-3 hours (Testing & Refinement)

**Total Estimated:** 10-15 hours

---

## Next Steps

1. Review and approve this plan
2. Start Phase 1: User Settings Layout
3. Follow TDD workflow
4. Create single PR with all phases
5. Update progress in `tasks.md` as we go

---

## Success Metrics

### Technical Success
- All tests passing (100%)
- No console errors
- No TypeScript errors
- Code formatted and clean
- Follows all project conventions

### User Experience Success
- Settings navigation is intuitive
- Active section is clearly visible
- Mobile experience is smooth
- Dark mode looks great
- Page transitions are smooth
- Layout is consistent with rest of app