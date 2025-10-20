# Task 7: Controllers Implementation

## Overview
**Task Reference:** Phase 7: Controllers from `agent-os/specs/20251015-admin-user-management/tasks.md`
**Implemented By:** API Engineer
**Date:** October 16, 2025
**Status:** ✅ Complete

### Task Description
Implement all controller classes for the Admin User Management feature, including user management, invitation handling, user activation/deactivation, role updates, and public invitation acceptance.

## Implementation Summary

Successfully implemented all 7 controller tasks for the Admin User Management feature following Laravel 12 best practices and the PeopleDear architecture patterns. The implementation uses:

- **Data Objects** instead of Form Requests for type-safe validation (Spatie Laravel Data v4)
- **Laravel 12's CurrentUser attribute** for clean dependency injection
- **Dependency injection** for Actions and Queries
- **Single Action Controllers** for focused operations (activate, deactivate, etc.)
- **Multi-Action Controllers** for related operations (UserController, InvitationController, AcceptInvitationController)

All controllers follow the flat hierarchy pattern (no nested Admin/ folders except where existing), use `final` classes, chain methods on new lines, and adhere to the project's coding standards.

## Files Changed/Created

### New Files
- `/app/Http/Controllers/ResendInvitationController.php` - Single action controller to resend invitations
- `/app/Http/Controllers/ActivateUserController.php` - Single action controller to activate users
- `/app/Http/Controllers/DeactivateUserController.php` - Single action controller to deactivate users
- `/app/Http/Controllers/UpdateUserRoleController.php` - Single action controller to update user roles
- `/app/Http/Controllers/AcceptInvitationController.php` - Public controller for invitation acceptance flow

### Modified Files
- `/app/Http/Controllers/Admin/UserController.php` - Updated to inject Queries and return paginated data
- `/app/Http/Controllers/InvitationController.php` - Already existed with Data Objects, verified correct implementation

## Key Implementation Details

### Task 7.1: UserController (Updated)
**Location:** `/app/Http/Controllers/Admin/UserController.php`

Updated the existing UserController to inject three Query classes via dependency injection and return proper Inertia responses with paginated users, pending invitations, and all available roles.

**Key Features:**
- Injects `UsersQuery`, `PendingInvitationsQuery`, and `RolesQuery`
- Paginates users (15 per page)
- Returns all data needed for the users management page
- Uses method chaining on new lines for readability

**Rationale:** Follows the Query pattern for data retrieval, keeping controllers lean and focused on orchestration.

---

### Task 7.2: InvitationController (Verified)
**Location:** `/app/Http/Controllers/InvitationController.php`

The InvitationController already existed and was correctly implemented using Data Objects. No changes were needed.

**Key Features:**
- Uses `CreateInvitationData` for type-safe validation
- Uses Laravel 12's `#[CurrentUser]` attribute
- Calls `CreateInvitation` action via dependency injection
- Returns proper redirect responses with success messages

**Rationale:** Already follows all architecture patterns and uses Data Objects instead of Form Requests.

---

### Task 7.3: ResendInvitationController
**Location:** `/app/Http/Controllers/ResendInvitationController.php`

Single action controller to resend invitations with expiration extension.

**Key Features:**
- Implements `__invoke()` method for single action
- Validates invitation hasn't been accepted before resending
- Returns proper error if invitation already accepted
- Uses `to_route()` helper for clean redirects

**Rationale:** Single responsibility - only handles resending invitations.

---

### Task 7.4: ActivateUserController
**Location:** `/app/Http/Controllers/ActivateUserController.php`

Single action controller to activate a user account.

**Key Features:**
- Implements `__invoke()` method
- Uses route model binding for `User`
- Delegates business logic to `ActivateUser` action
- Returns redirect with success message using `to_route()`

**Rationale:** Keeps controller thin - all business logic in Action class.

---

### Task 7.5: DeactivateUserController
**Location:** `/app/Http/Controllers/DeactivateUserController.php`

Single action controller to deactivate a user account.

**Key Features:**
- Implements `__invoke()` method
- Uses route model binding for `User`
- Delegates business logic to `DeactivateUser` action
- Returns redirect with success message

**Rationale:** Mirrors ActivateUserController pattern for consistency.

---

### Task 7.6: UpdateUserRoleController
**Location:** `/app/Http/Controllers/UpdateUserRoleController.php`

Single action controller to update a user's role.

**Key Features:**
- Uses `UpdateUserRoleData` Data Object for validation
- Type-hints Data Object in method signature (Laravel auto-validates)
- Extracts `role_id` from Data Object and passes to Action
- Returns redirect with success message

**Rationale:** Uses Data Objects for validation instead of Form Requests, following project standards.

---

### Task 7.7: AcceptInvitationController
**Location:** `/app/Http/Controllers/AcceptInvitationController.php`

Public controller handling invitation acceptance flow (guest routes).

**Key Features:**
- Two methods: `show()` for displaying form, `store()` for processing
- Uses `AcceptInvitationData` Data Object for validation
- Queries invitation by token
- Validates invitation not expired (returns 410 Gone for expired)
- Returns Inertia response for registration form
- Redirects to dashboard after successful registration

**Implementation Details:**
```php
public function show(string $token): Response
{
    $invitation = Invitation::query()
        ->with('role')
        ->where('token', $token)
        ->whereNull('accepted_at')
        ->firstOrFail();

    if ($invitation->isExpired()) {
        abort(410, 'This invitation has expired.');
    }

    return Inertia::render('AcceptInvitation', [
        'invitation' => [
            'email' => $invitation->email,
            'role' => $invitation->role->display_name,
            'token' => $invitation->token,
        ],
    ]);
}

public function store(
    AcceptInvitationData $data,
    string $token,
    AcceptInvitation $acceptInvitation
): RedirectResponse {
    $invitation = Invitation::query()
        ->where('token', $token)
        ->whereNull('accepted_at')
        ->firstOrFail();

    if ($invitation->isExpired()) {
        return redirect()
            ->route('auth.login.index')
            ->withErrors([
                'token' => __('This invitation has expired.'),
            ]);
    }

    $acceptInvitation->handle(
        invitation: $invitation,
        name: $data->name,
        password: $data->password
    );

    return redirect()
        ->route('dashboard')
        ->with('success', __('Welcome to PeopleDear!'));
}
```

**Rationale:** Separates display logic from processing logic. Uses Data Objects for automatic validation. Provides clear error states for expired invitations.

## Database Changes
None - controllers only orchestrate existing Actions and Queries.

## Dependencies
No new dependencies added. All controllers use existing Actions, Queries, and Data Objects.

## Testing

### Test Files Created/Updated
None - testing is handled in Phase 13 (Feature Tests) and Phase 14 (Browser Tests).

### Test Coverage
- Unit tests: N/A (controllers will be tested via feature tests)
- Integration tests: Pending Phase 13
- Edge cases covered: Pending Phase 13

### Manual Testing Performed
- Verified all controllers compile without syntax errors
- Ran `vendor/bin/pint --dirty` to ensure code formatting compliance
- All files formatted successfully with 3 style issues fixed automatically

## User Standards & Preferences Compliance

### /agent-os/standards/backend/api.md
**How Implementation Complies:**
All controllers follow the architecture patterns defined in the standards:
- Controllers inject Actions and Queries via dependency injection
- Business logic is delegated to Action classes, keeping controllers lean
- Controllers return appropriate response types (RedirectResponse, Inertia Response)
- Use `to_route()` helper for clean redirects
- Single Action Controllers use `__invoke()` method

**Deviations:** None

---

### /agent-os/standards/global/coding-style.md
**How Implementation Complies:**
- All classes are marked `final`
- Methods chain on new lines for readability
- Explicit return type declarations on all methods
- Proper use of `use function` imports (`to_route`, `abort`)
- All code formatted with Laravel Pint

**Deviations:** None

---

### /agent-os/standards/global/conventions.md
**How Implementation Complies:**
- Controller classes follow PeopleDear's flat hierarchy pattern (no nested folders except existing Admin/)
- Single Action Controllers for focused operations
- Multi-Action Controllers for related operations
- Descriptive class names without unnecessary nesting
- Use of Laravel 12's `#[CurrentUser]` attribute

**Deviations:** None

---

### /agent-os/standards/backend/models.md
**How Implementation Complies:**
Controllers properly use route model binding and don't contain model logic. All model updates are delegated to Action classes following the lean model philosophy.

**Deviations:** None

---

### /Users/franciscobarrento/Codex/PeopleDear/peopledear/CLAUDE.md - Laravel Data
**How Implementation Complies:**
- **ALWAYS use Data objects for validation** - All controllers use Data Objects (CreateInvitationData, UpdateUserRoleData, AcceptInvitationData) instead of Form Requests
- Data Objects are type-hinted in controller methods for automatic validation
- Laravel automatically validates and injects Data Objects
- Properties accessed directly from Data Objects (e.g., `$data->email`, `$data->role_id`)

**Deviations:** None

---

### /Users/franciscobarrento/Codex/PeopleDear/peopledear/CLAUDE.md - Laravel 12 CurrentUser Attribute
**How Implementation Complies:**
- InvitationController uses `#[CurrentUser] User $user` instead of `Request::user()`
- More explicit and readable than manual injection
- Type-safe dependency injection

**Deviations:** None - All controllers that need the current user use the CurrentUser attribute.

## Integration Points

### APIs/Endpoints
All controllers integrate with routes defined in Phase 8. Controllers expect these route names:
- `users.index` - Users index page (redirect target for most actions)
- `auth.login.index` - Login page (redirect for expired invitations)
- `dashboard` - Dashboard page (redirect after accepting invitation)

### Internal Dependencies
Controllers depend on:
- **Actions:** `CreateInvitation`, `ResendInvitation`, `AcceptInvitation`, `ActivateUser`, `DeactivateUser`, `UpdateUserRole`
- **Queries:** `UsersQuery`, `PendingInvitationsQuery`, `RolesQuery`
- **Data Objects:** `CreateInvitationData`, `UpdateUserRoleData`, `AcceptInvitationData`
- **Models:** `User`, `Invitation` (for route model binding and querying)

All dependencies are injected via constructor or method parameters using Laravel's service container.

## Known Issues & Limitations

### Issues
None

### Limitations
1. **No Authorization Checks in Controllers**
   - Description: Controllers don't verify admin permissions - this is handled by AdminMiddleware
   - Reason: Separation of concerns - middleware handles authorization
   - Future Consideration: None needed - this is the intended design

2. **No Validation Messages Customization in Controllers**
   - Description: Validation messages are defined in Data Objects, not controllers
   - Reason: Data Objects own validation logic and messages
   - Future Consideration: This is the intended pattern

## Performance Considerations
- UserController uses pagination (15 users per page) to avoid loading large datasets
- Queries use eager loading to prevent N+1 problems
- Controllers delegate to Actions which can be optimized independently

## Security Considerations
- All admin controllers protected by AdminMiddleware (registered in Phase 3)
- AcceptInvitationController validates tokens and expiration before processing
- Returns HTTP 410 Gone for expired invitations (semantic and cacheable)
- Uses Laravel's validation via Data Objects to prevent injection attacks
- All user inputs validated before reaching Action classes

## Dependencies for Other Tasks
- **Phase 8 (Routes):** Controllers need routes registered to be accessible
- **Phase 13 (Feature Tests):** Tests will verify controller behavior
- **Phase 10-11 (Frontend):** Frontend pages need to call these controller endpoints

## Notes

### Why Data Objects Instead of Form Requests?
The existing codebase uses Spatie Laravel Data v4 for validation and data transfer. This provides:
- Type-safe data handling with IDE autocompletion
- Automatic validation using attributes
- Automatic casting of data types
- Combines validation + DTOs in one class
- Better integration with Inertia.js

### Laravel 12 CurrentUser Attribute Pattern
Laravel 12's contextual attributes provide cleaner dependency injection:
```php
// ✅ CORRECT - Use CurrentUser attribute
use Illuminate\Container\Attributes\CurrentUser;

public function store(
    CreateInvitationData $data,
    CreateInvitation $action,
    #[CurrentUser] User $user
): RedirectResponse {
    $invitation = $action->handle($data->email, $data->role_id, $user->id);
    return to_route('users.index');
}
```

This is more explicit, type-safe, and works everywhere dependency injection is supported.

### Single Action Controllers vs Multi-Action
The implementation follows Laravel best practices:
- **Single Action Controllers** for focused, single-purpose operations (activate, deactivate, resend, update role)
- **Multi-Action Controllers** for related operations that share context (UserController with index, InvitationController with store/destroy, AcceptInvitationController with show/store)

### Code Formatting
All controllers were formatted using Laravel Pint (`vendor/bin/pint --dirty`) with 3 style issues automatically fixed:
- Extra blank lines removed
- Import statements optimized
- Code style standardized

## Next Steps
1. **Phase 8:** Register all controller routes in `routes/web.php`
2. **Phase 9:** Create email notification system for invitations
3. **Phase 10-11:** Create frontend pages that call these controllers
4. **Phase 13:** Write comprehensive feature tests for all controllers