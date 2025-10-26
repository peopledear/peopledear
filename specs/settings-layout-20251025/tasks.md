# Settings Layout - Detailed Tasks & Progress

**Project:** PeopleDear
**Spec:** Settings Layout Implementation
**Last Updated:** 2025-10-25

---

## Progress Overview

- [ ] Phase 1: User Settings Layout
- [ ] Phase 2: Migrate User Settings Pages
- [ ] Phase 3: Organization Settings Layout
- [ ] Phase 4: Migrate Organization Settings Page
- [ ] Phase 5: Testing & Refinement

---

## Phase 1: User Settings Layout

**Branch:** `feature/settings-layout`
**Status:** ⏳ Not Started
**Estimated Time:** 3-4 hours

### Setup
- [ ] Checkout main: `git checkout main`
- [ ] Pull latest: `git pull origin main`
- [ ] Create branch: `git checkout -b feature/settings-layout`

### Browser Tests (TDD - Write First)
- [ ] Create test file: `tests/Browser/UserSettingsLayoutTest.php`
- [ ] Test: Layout renders for authenticated user
  ```php
  test('user settings layout renders for authenticated user')
  ```
- [ ] Test: All navigation sections visible (Profile, Password, Appearance, Security)
  ```php
  test('user settings navigation shows all sections')
  ```
- [ ] Test: Active section highlighted correctly
  ```php
  test('active section is highlighted in navigation')
  ```
- [ ] Test: Navigation between sections works
  ```php
  test('can navigate between settings sections')
  ```
- [ ] Test: Mobile sidebar drawer opens and closes
  ```php
  test('mobile navigation drawer works')
  ```
- [ ] Test: Dark mode styling works
  ```php
  test('user settings layout supports dark mode')
  ```

### Implementation - Layout Component
- [ ] Create file: `resources/js/layouts/user-settings-layout.tsx`
- [ ] Define TypeScript interface for props:
  ```typescript
  interface UserSettingsLayoutProps {
    children: ReactNode;
    activeSection?: 'profile' | 'password' | 'appearance' | 'security';
  }
  ```
- [ ] Implement desktop sidebar:
  - [ ] Container with flex layout
  - [ ] Sidebar column (w-64 on desktop)
  - [ ] Navigation items with links
  - [ ] Active state highlighting
  - [ ] Proper spacing and styling
- [ ] Implement mobile drawer:
  - [ ] Use shadcn Sheet component
  - [ ] Menu button for mobile
  - [ ] Same navigation items as desktop
  - [ ] Hidden on desktop (md:hidden)
- [ ] Add breadcrumbs:
  - [ ] "Settings > [Section Name]" format
  - [ ] Dynamic based on activeSection prop
- [ ] Add main content area:
  - [ ] Flex-1 to fill remaining space
  - [ ] Proper padding and container
  - [ ] Max-width constraint
  - [ ] Scrollable overflow
- [ ] Style with Tailwind v4:
  - [ ] Responsive classes (hidden md:flex, etc.)
  - [ ] Dark mode classes (dark:bg-*, dark:text-*, etc.)
  - [ ] Proper spacing (gap, padding, margin)
  - [ ] Border and separator styling
- [ ] Add navigation helper:
  - [ ] Use Inertia Link component
  - [ ] Detect active route
  - [ ] Apply active styling

### Verification - Phase 1
- [ ] All browser tests pass
- [ ] Layout renders correctly in browser (manual check)
- [ ] Desktop sidebar shows and works
- [ ] Mobile drawer shows and works
- [ ] Active highlighting works correctly
- [ ] Responsive behavior correct (test at 320px, 768px, 1024px, 1920px)
- [ ] Dark mode looks good (toggle and verify)
- [ ] No TypeScript errors: `npm run build`
- [ ] No console errors in browser
- [ ] Code formatted: `vendor/bin/pint --dirty`

---

## Phase 2: Migrate User Settings Pages

**Status:** ⏳ Not Started
**Estimated Time:** 2-3 hours
**Dependencies:** Phase 1 completed

### Update Profile Page
- [ ] Open file: `resources/js/pages/user-profile/edit.tsx`
- [ ] Import UserSettingsLayout
- [ ] Import Card components: `Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter`
- [ ] Wrap content with `<UserSettingsLayout activeSection="profile">`
- [ ] **Organize content into cards:**
  - [ ] "Profile Information" Card - name, email fields with Save button
  - [ ] "Delete Account" Card (Danger) - destructive action with red border/styling
- [ ] Remove any duplicate headers/breadcrumbs
- [ ] Test page renders correctly with card layout
- [ ] Test form still works
- [ ] Test cards have proper spacing (`space-y-6`)

### Update Password Page
- [ ] Open file: `resources/js/pages/user-password/edit.tsx`
- [ ] Import UserSettingsLayout
- [ ] Import Card components: `Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter`
- [ ] Wrap content with `<UserSettingsLayout activeSection="password">`
- [ ] **Organize content into cards:**
  - [ ] "Update Password" Card - current password, new password, confirm password fields with Update button
- [ ] Remove any duplicate headers/breadcrumbs
- [ ] Test page renders correctly with card layout
- [ ] Test form still works

### Update Appearance Page
- [ ] Open file: `resources/js/pages/appearance/update.tsx`
- [ ] Import UserSettingsLayout
- [ ] Import Card components: `Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter`
- [ ] Wrap content with `<UserSettingsLayout activeSection="appearance">`
- [ ] **Organize content into cards:**
  - [ ] "Appearance" Card - theme selector (light/dark/system) with description
- [ ] Remove any duplicate headers/breadcrumbs
- [ ] Test page renders correctly with card layout
- [ ] Test theme toggle functionality still works

### Update Two-Factor Authentication Page
- [ ] Open file: `resources/js/pages/user-two-factor-authentication/show.tsx`
- [ ] Import UserSettingsLayout
- [ ] Import Card components: `Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter`
- [ ] Wrap content with `<UserSettingsLayout activeSection="security">`
- [ ] **Organize content into cards:**
  - [ ] "Two-Factor Authentication" Card - enable/disable 2FA, QR code, recovery codes
  - [ ] "Active Sessions" Card (optional if exists) - list of active sessions
- [ ] Remove any duplicate headers/breadcrumbs
- [ ] Test page renders correctly with card layout
- [ ] Test 2FA setup/disable functionality still works

### Browser Tests - User Settings Navigation
- [ ] Create test file: `tests/Browser/UserSettingsNavigationTest.php`
- [ ] Test: Can navigate from profile to password
  ```php
  test('can navigate from profile to password settings')
  ```
- [ ] Test: Can navigate from password to appearance
  ```php
  test('can navigate from password to appearance settings')
  ```
- [ ] Test: Can navigate from appearance to security
  ```php
  test('can navigate from appearance to security settings')
  ```
- [ ] Test: Active section updates when navigating
  ```php
  test('active section updates when navigating between pages')
  ```
- [ ] Test: All existing functionality still works on each page
  ```php
  test('profile form still works with new layout')
  test('password form still works with new layout')
  test('appearance toggle still works with new layout')
  test('two factor setup still works with new layout')
  ```

### Verification - Phase 2
- [ ] All pages use new layout
- [ ] Navigation between sections works smoothly
- [ ] Active highlighting updates correctly
- [ ] All existing forms and functionality work
- [ ] No visual regressions
- [ ] All browser tests pass
- [ ] All feature tests still pass
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] Code formatted

---

## Phase 3: Organization Settings Layout

**Status:** ⏳ Not Started
**Estimated Time:** 2-3 hours
**Dependencies:** Phase 1 (for consistency)

### Browser Tests (TDD - Write First)
- [ ] Create test file: `tests/Browser/OrgSettingsLayoutTest.php`
- [ ] Test: Layout renders for people_manager role
  ```php
  test('org settings layout renders for people manager')
  ```
- [ ] Test: Layout renders for owner role
  ```php
  test('org settings layout renders for owner')
  ```
- [ ] Test: Layout forbidden for employee role (redirect or 403)
  ```php
  test('org settings layout forbidden for employee')
  ```
- [ ] Test: All navigation sections visible
  ```php
  test('org settings navigation shows all sections')
  ```
- [ ] Test: Future sections shown as disabled
  ```php
  test('future sections are visible but disabled')
  ```
- [ ] Test: Active section highlighted correctly
  ```php
  test('active org section is highlighted in navigation')
  ```
- [ ] Test: Mobile sidebar drawer works
  ```php
  test('org settings mobile navigation drawer works')
  ```
- [ ] Test: Dark mode styling works
  ```php
  test('org settings layout supports dark mode')
  ```

### Implementation - Layout Component
- [ ] Create file: `resources/js/layouts/org-settings-layout.tsx`
- [ ] Define TypeScript interface for props:
  ```typescript
  interface OrgSettingsLayoutProps {
    children: ReactNode;
    activeSection?: 'general' | 'offices' | 'billing' | 'team';
  }
  ```
- [ ] Implement desktop sidebar:
  - [ ] Container with flex layout (similar to user settings)
  - [ ] Sidebar column (w-64 on desktop)
  - [ ] Navigation items:
    - [ ] General (enabled)
    - [ ] Offices (disabled, gray, cursor-not-allowed)
    - [ ] Billing (disabled, gray, cursor-not-allowed)
    - [ ] Team (disabled, gray, cursor-not-allowed)
  - [ ] Active state highlighting
  - [ ] Proper spacing and styling
- [ ] Implement mobile drawer:
  - [ ] Use shadcn Sheet component
  - [ ] Menu button for mobile
  - [ ] Same navigation items as desktop
  - [ ] Hidden on desktop (md:hidden)
- [ ] Add breadcrumbs:
  - [ ] "Organization Settings > [Section Name]" format
  - [ ] Dynamic based on activeSection prop
- [ ] Add main content area:
  - [ ] Flex-1 to fill remaining space
  - [ ] Proper padding and container
  - [ ] Max-width constraint
  - [ ] Scrollable overflow
- [ ] Style with Tailwind v4:
  - [ ] Match user settings layout styling
  - [ ] Responsive classes
  - [ ] Dark mode classes
  - [ ] Disabled state styling for future sections
  - [ ] Proper spacing
- [ ] Add tooltips for disabled sections (optional):
  - [ ] "Coming soon" tooltip on hover

### Verification - Phase 3
- [ ] All browser tests pass
- [ ] Layout renders correctly for authorized users
- [ ] Unauthorized users get 403 or redirect
- [ ] Desktop sidebar shows and works
- [ ] Mobile drawer shows and works
- [ ] Active highlighting works correctly
- [ ] Disabled sections shown correctly
- [ ] Responsive behavior correct
- [ ] Dark mode looks good
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] Code formatted

---

## Phase 4: Migrate Organization Settings Page

**Status:** ⏳ Not Started
**Estimated Time:** 1-2 hours
**Dependencies:** Phase 3 completed

### Update Organization General Page
- [ ] Open file: `resources/js/pages/org-settings-general/edit.tsx`
- [ ] Import OrgSettingsLayout
- [ ] Import Card components: `Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter`
- [ ] Replace AdminLayout with `<OrgSettingsLayout activeSection="general">`
- [ ] **Organize content into cards (matching reference image):**
  - [ ] "General" Card - organization name, VAT, SSN, phone fields
    - [ ] CardHeader with title "General" and description
    - [ ] CardContent with form fields using `space-y-6` for sections
    - [ ] Each field has Label, Input, and helper text
  - [ ] "Danger" Card - destructive actions (if applicable)
    - [ ] Red border (`border-destructive`)
    - [ ] Red title (`text-destructive`)
    - [ ] Destructive button variant
- [ ] Remove any duplicate headers/breadcrumbs
- [ ] Keep all existing functionality
- [ ] Test page renders correctly with card layout matching reference image
- [ ] Test form still works
- [ ] Verify visual match to `specs/settings-layout-20251025/img.png`

### Browser Tests - Organization Settings
- [ ] Create/update test file: `tests/Browser/OrgSettingsNavigationTest.php`
- [ ] Test: Organization general page uses new layout
  ```php
  test('organization general page uses org settings layout')
  ```
- [ ] Test: Navigation sidebar visible
  ```php
  test('organization settings sidebar visible on general page')
  ```
- [ ] Test: General section is active
  ```php
  test('general section is active on organization general page')
  ```
- [ ] Test: Form still works with new layout
  ```php
  test('organization form still works with new layout')
  ```
- [ ] Test: Authorization still works
  ```php
  test('people manager can access organization settings')
  test('owner can access organization settings')
  test('employee cannot access organization settings')
  ```

### Verification - Phase 4
- [ ] Page uses new layout
- [ ] Sidebar navigation shows
- [ ] Active section highlighted correctly
- [ ] All existing functionality works
- [ ] Form submission works
- [ ] Authorization works correctly
- [ ] No visual regressions
- [ ] All browser tests pass
- [ ] All feature tests still pass
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] Code formatted

---

## Phase 5: Testing & Refinement

**Status:** ⏳ Not Started
**Estimated Time:** 2-3 hours
**Dependencies:** Phases 1-4 completed

### Comprehensive Browser Testing
- [ ] Test all user settings pages in sequence:
  - [ ] Navigate profile → password → appearance → security → profile
  - [ ] Verify active highlighting updates each time
  - [ ] Verify all forms work
  - [ ] Verify no console errors
- [ ] Test organization settings:
  - [ ] Navigate to org general
  - [ ] Verify sidebar shows
  - [ ] Verify form works
  - [ ] Verify authorization
- [ ] Test responsive behavior:
  - [ ] Test at 320px width (mobile)
  - [ ] Test at 768px width (tablet)
  - [ ] Test at 1024px width (desktop)
  - [ ] Test at 1920px width (large desktop)
  - [ ] Verify sidebar collapses/expands correctly
  - [ ] Verify mobile drawer works
- [ ] Test dark mode:
  - [ ] Toggle dark mode on each settings page
  - [ ] Verify styling looks good
  - [ ] Verify text is readable
  - [ ] Verify borders and separators visible
  - [ ] Verify active states visible
- [ ] Test authorization:
  - [ ] User can access all user settings
  - [ ] People Manager can access org settings
  - [ ] Owner can access org settings
  - [ ] Employee cannot access org settings (403/redirect)

### Performance Testing
- [ ] Test page load times (should be fast)
- [ ] Test navigation speed between sections
- [ ] Verify no layout shift on page load
- [ ] Verify smooth transitions
- [ ] Check for any memory leaks (browser dev tools)

### Accessibility Testing
- [ ] Test keyboard navigation:
  - [ ] Tab through navigation items
  - [ ] Enter to activate links
  - [ ] Escape to close mobile drawer
- [ ] Test screen reader (basic check):
  - [ ] Navigation items have proper labels
  - [ ] Active state is announced
  - [ ] Breadcrumbs are readable
- [ ] Test focus states:
  - [ ] Focus visible on navigation items
  - [ ] Focus trapped in mobile drawer when open

### UX Refinement
- [ ] Review spacing and alignment
  - [ ] Sidebar items properly spaced
  - [ ] Content area properly padded
  - [ ] Breadcrumbs properly positioned
- [ ] Review typography
  - [ ] Font sizes appropriate
  - [ ] Font weights appropriate
  - [ ] Line heights comfortable
- [ ] Review colors
  - [ ] Active states clearly visible
  - [ ] Disabled states clearly different
  - [ ] Dark mode colors appropriate
  - [ ] Contrast ratios good (WCAG AA)
- [ ] Review animations/transitions
  - [ ] Smooth and not jarring
  - [ ] Appropriate duration
  - [ ] Respect prefers-reduced-motion
- [ ] Get feedback (if possible):
  - [ ] Show to team member
  - [ ] Get UX feedback
  - [ ] Make adjustments if needed

### Code Quality Review
- [ ] Review TypeScript types:
  - [ ] All props properly typed
  - [ ] No `any` types
  - [ ] Interfaces exported if needed
- [ ] Review component structure:
  - [ ] Components properly organized
  - [ ] No duplicate code
  - [ ] Reusable logic extracted
- [ ] Review styling:
  - [ ] Tailwind classes organized
  - [ ] No inline styles (unless needed)
  - [ ] Responsive classes correct
  - [ ] Dark mode classes correct
- [ ] Review tests:
  - [ ] All important flows covered
  - [ ] Tests well organized
  - [ ] Test names descriptive
  - [ ] No flaky tests

### Final Verification
- [ ] Run full test suite: `php artisan test`
  - [ ] All tests pass
  - [ ] No warnings or errors
- [ ] Format code: `vendor/bin/pint --dirty`
  - [ ] All files formatted
  - [ ] No formatting issues
- [ ] Build frontend: `npm run build`
  - [ ] Build succeeds
  - [ ] No TypeScript errors
  - [ ] No build warnings
- [ ] Manual smoke test:
  - [ ] Visit each settings page
  - [ ] Test navigation
  - [ ] Test one form on each page
  - [ ] Toggle dark mode
  - [ ] Test on mobile (Chrome DevTools)
- [ ] Take screenshots for PR:
  - [ ] User settings - desktop light mode
  - [ ] User settings - desktop dark mode
  - [ ] User settings - mobile
  - [ ] Org settings - desktop light mode
  - [ ] Org settings - desktop dark mode
  - [ ] Org settings - mobile
  - [ ] Navigation flow (animated GIF if possible)

---

## PR & Deployment

### Before Creating PR
- [ ] All tests passing
- [ ] Code formatted
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] Screenshots taken
- [ ] Manual testing completed

### Create Pull Request
- [ ] Commit all changes with descriptive message:
  ```bash
  git add .
  git commit -m "feat: add unified settings layouts with sidebar navigation

  - Create UserSettingsLayout with sidebar for user settings
  - Create OrgSettingsLayout with sidebar for org settings
  - Migrate all user settings pages to new layout
  - Migrate organization settings page to new layout
  - Add responsive mobile drawer navigation
  - Add dark mode support throughout
  - Add comprehensive browser tests for navigation

  🤖 Generated with [Claude Code](https://claude.com/claude-code)

  Co-Authored-By: Claude <noreply@anthropic.com>"
  ```
- [ ] Push branch: `git push origin feature/settings-layout`
- [ ] Create PR on GitHub
- [ ] Fill in PR description:
  - [ ] Summary of changes
  - [ ] Screenshots (before/after, light/dark, mobile/desktop)
  - [ ] Test coverage summary
  - [ ] UX improvements
  - [ ] Migration notes
  - [ ] Link to spec: `specs/settings-layout-20251025/`
- [ ] Request review from team

### PR Description Template
```markdown
# Settings Layout Implementation

## Summary
Implements unified settings layouts with sidebar navigation for both user settings and organization settings, providing a consistent and intuitive settings experience.

## Changes
- ✅ Created `UserSettingsLayout` with responsive sidebar navigation
- ✅ Created `OrgSettingsLayout` with responsive sidebar navigation
- ✅ Migrated all user settings pages to new layout
- ✅ Migrated organization settings page to new layout
- ✅ Added mobile drawer navigation
- ✅ Added dark mode support
- ✅ Added comprehensive browser tests

## Screenshots
### User Settings - Desktop Light Mode
[Screenshot]

### User Settings - Desktop Dark Mode
[Screenshot]

### User Settings - Mobile
[Screenshot]

### Organization Settings - Desktop Light Mode
[Screenshot]

### Organization Settings - Desktop Dark Mode
[Screenshot]

### Organization Settings - Mobile
[Screenshot]

## Test Coverage
- Browser tests: X tests, Y assertions
- Feature tests: All existing tests still pass
- Manual testing: Completed on Chrome, Safari, Firefox
- Responsive testing: Tested 320px, 768px, 1024px, 1920px
- Dark mode: Tested throughout

## UX Improvements
- Consistent navigation across all settings pages
- Clear active section highlighting
- Smooth transitions between sections
- Mobile-friendly drawer navigation
- Breadcrumbs for navigation context

## Migration Notes
- All existing settings pages now use new layouts
- No breaking changes to functionality
- All existing routes unchanged
- Authorization still works as before

## Spec
See detailed spec in `specs/settings-layout-20251025/`
```

### After PR Merged
- [ ] Update progress: Mark all phases as ✅ in this file
- [ ] Close the spec (if workflow exists)
- [ ] Celebrate! 🎉

---

## Global Checklist

### Code Quality Standards
- [ ] All variables type hinted (TypeScript/PHPDoc)
- [ ] All imports organized
- [ ] All components properly typed
- [ ] Methods chained on new lines
- [ ] Consistent code style
- [ ] No commented-out code
- [ ] No console.log statements
- [ ] No TODO comments (create issues instead)

### Testing Standards
- [ ] Browser tests for all user flows
- [ ] Feature tests for authorization
- [ ] All tests pass
- [ ] Tests well organized
- [ ] Test names descriptive
- [ ] No flaky tests

### Documentation
- [ ] Code comments for complex logic
- [ ] TypeScript interfaces well documented
- [ ] PR description comprehensive
- [ ] Screenshots included
- [ ] Spec updated if scope changed

---

## Notes & Decisions

### Decision Log
- **2025-10-25:** Create separate UserSettingsLayout and OrgSettingsLayout instead of one shared layout
  - **Reason:** Different navigation items, may diverge in future, clearer separation of concerns

- **2025-10-25:** Use Inertia persistent layouts
  - **Reason:** Avoid re-rendering sidebar on navigation between sections

- **2025-10-25:** Show future org settings sections as disabled
  - **Reason:** Give users visibility into what's coming, clear roadmap

- **2025-10-25:** Use shadcn Sheet component for mobile drawer
  - **Reason:** Proven component, handles accessibility, animations, focus management

### Questions & Answers
- **Q:** Should we create a shared SettingsSidebar component?
  - **A:** Not initially. Keep layouts separate since they have different nav items. Can refactor later if significant duplication.

- **Q:** Should disabled sections be clickable with a "coming soon" message?
  - **A:** No, just show as disabled with cursor-not-allowed. Keep it simple.

- **Q:** Should we add route transition animations?
  - **A:** No, Inertia's default behavior is good. Don't over-animate.

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
2. Review `requirements.md` for context
3. Review `plan.md` for architecture decisions
4. Continue with next unchecked task
5. Update checkboxes as you complete tasks
6. Update phase status when phase completes
7. Keep tests passing at all times

---

## Estimated Timeline

- **Phase 1:** 3-4 hours (User Settings Layout)
- **Phase 2:** 2-3 hours (Migrate User Settings)
- **Phase 3:** 2-3 hours (Org Settings Layout)
- **Phase 4:** 1-2 hours (Migrate Org Settings)
- **Phase 5:** 2-3 hours (Testing & Refinement)

**Total:** 10-15 hours

**Current Time Spent:** 0 hours

---

## Success Criteria

### Implementation Complete When:
- [ ] All 5 phases completed
- [ ] All tasks checked off
- [ ] All tests passing
- [ ] Code formatted
- [ ] No TypeScript errors
- [ ] No console errors
- [ ] PR created with screenshots
- [ ] PR approved and merged

### Quality Metrics:
- [ ] Test coverage: Comprehensive browser tests for all flows
- [ ] Performance: Fast page loads and navigation
- [ ] Accessibility: Keyboard navigation works, focus management good
- [ ] Responsive: Works on mobile, tablet, desktop
- [ ] Dark mode: Looks good in both light and dark
- [ ] Code quality: Clean, typed, well-organized
- [ ] UX: Intuitive navigation, clear active states, smooth transitions