# Admin User Management - Implementation Tasks

**Spec**: `admin-user-management.md`
**Status**: Ready for Implementation
**Created**: October 15, 2025

---

## Task Breakdown

This document provides a granular, step-by-step implementation plan for the Admin User Management feature.

---

## Phase 1: Role Model (Foundation - No Dependencies)

### Task 1.1: Create Role Model with Migration, Factory, Seeder
**Files**: `app/Models/Role.php`, migration, factory, seeder
**Estimated Time**: 30 minutes

**Steps**:
1. Run `php artisan make:model Role -mfs --no-interaction`
2. **Update Migration** (`database/migrations/YYYY_MM_DD_HHMMSS_create_roles_table.php`):
   - `id` (id() - comes first)
   - `name` (string, unique) - e.g., 'admin', 'manager', 'employee'
   - `display_name` (string) - e.g., 'Administrator', 'Manager', 'Employee'
   - `description` (text, nullable)
   - `timestamps` (comes after all other columns)
3. **Update Model** (`app/Models/Role.php`):
   - Add PHPDoc properties (`@property-read`)
   - Add `users()` relationship (HasMany)
   - Make class `final`
4. **Update Factory** (`database/factories/RoleFactory.php`):
   - Define fake data for name, display_name, description
5. **Update Seeder** (`database/seeders/RoleSeeder.php`):
   - Create three roles:
     - Admin: name='admin', display_name='Administrator', description='Full system access'
     - Manager: name='manager', display_name='Manager', description='Can approve requests and view team data'
     - Employee: name='employee', display_name='Employee', description='Can submit requests and view own data'
6. Run migration: `php artisan migrate`
7. Run seeder: `php artisan db:seed --class=RoleSeeder`

**Verification**:
- Database has `roles` table
- Query `Role::all()` returns 3 roles
- Timestamps are after all other columns

---

### Task 1.2: Create Role Model Tests
**File**: `tests/Unit/Models/RoleTest.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:test Unit/Models/RoleTest --unit --pest --no-interaction`
2. Write tests:
   - Test role can be created
   - Test `users()` relationship exists (hasMany)
   - Test database has correct columns (id, name, display_name, description, timestamps)
   - Test name is unique constraint
   - Test timestamps are present
3. Run tests: `php artisan test --filter=RoleTest`

**Verification**: All tests pass

---

## Phase 2: Update User Model (Depends on Role)

### Task 2.1: Add Role Relationship to User Model
**Files**: `app/Models/User.php`, migration
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:migration add_role_to_users_table --no-interaction`
2. **Update Migration**:
   - Add `$table->foreignIdFor(Role::class)->nullable()->after('avatar')->constrained()->nullOnDelete();`
   - Add `$table->boolean('is_active')->default(true)->after('role_id');`
   - Remember: timestamps should be last
3. **Update User Model**:
   - Add `role()` relationship (BelongsTo Role)
   - Add role helper methods:
     - `isAdmin()` - returns true if role?->name === 'admin'
     - `isManager()` - returns true if role?->name === 'manager'
     - `isEmployee()` - returns true if role?->name === 'employee'
4. Run migration: `php artisan migrate`

**Verification**:
- Users table has `role_id` and `is_active` columns
- Can query user with role: `User::with('role')->first()`

---

### Task 2.2: Update User Model Tests
**File**: `tests/Unit/Models/UserTest.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Update existing UserTest or create if doesn't exist
2. Add tests:
   - Test `role()` relationship exists (belongsTo)
   - Test `isAdmin()` helper method
   - Test `isManager()` helper method
   - Test `isEmployee()` helper method
   - Test `is_active` column exists and defaults to true
3. Run tests: `php artisan test --filter=UserTest`

**Verification**: All tests pass

---

## Phase 3: Invitation Model (Depends on Role and User)

### Task 3.1: Create Invitation Model with Migration, Factory, Seeder
**Files**: `app/Models/Invitation.php`, migration, factory, seeder
**Estimated Time**: 45 minutes

**Steps**:
1. Run `php artisan make:model Invitation -mfs --no-interaction`
2. **Update Migration** (`database/migrations/YYYY_MM_DD_HHMMSS_create_invitations_table.php`):
   - `id` (id() - comes first)
   - `email` (string()->index())
   - `token` (string()->unique())
   - `$table->foreignIdFor(Role::class)->constrained()->cascadeOnDelete();`
   - `$table->foreignIdFor(User::class, 'invited_by')->constrained('users')->cascadeOnDelete();`
   - `accepted_at` (timestamp()->nullable())
   - `expires_at` (timestamp())
   - `timestamps` (comes last)
   - Add unique constraint: `$table->unique(['email', 'accepted_at']);`
3. **Update Model** (`app/Models/Invitation.php`):
   - Add PHPDoc properties (`@property-read`)
   - Define `casts()` method (accepted_at, expires_at as datetime)
   - Add relationships:
     - `role()` (BelongsTo Role)
     - `inviter()` (BelongsTo User, foreign key 'invited_by')
   - Add business logic methods:
     - `isPending()` - returns true if accepted_at is null && !isExpired()
     - `isExpired()` - returns true if expires_at->isPast()
     - `isAccepted()` - returns true if accepted_at !== null
   - Add `booted()` method:
     - Generate UUID token on creating if not set: `Str::uuid()->toString()`
     - Set expires_at to now()->addDays(7) if not set
   - Make class `final`
4. **Update Factory** (`database/factories/InvitationFactory.php`):
   - Define fake data for email, token (UUID), role_id, invited_by, expires_at
   - accepted_at should be null by default
5. **Update Seeder** (optional, only if needed for testing)
6. Run migration: `php artisan migrate`

**Verification**:
- Database has `invitations` table with correct foreign keys
- Token auto-generates on creation
- expires_at sets to 7 days from now

---

### Task 3.2: Create Invitation Model Tests
**File**: `tests/Unit/Models/InvitationTest.php`
**Estimated Time**: 30 minutes

**Steps**:
1. Run `php artisan make:test Unit/Models/InvitationTest --unit --pest --no-interaction`
2. Write tests:
   - Test invitation can be created
   - Test `role()` relationship exists (belongsTo)
   - Test `inviter()` relationship exists (belongsTo User)
   - Test token auto-generates on creation (UUID format)
   - Test expires_at auto-sets to 7 days from now on creation
   - Test `isPending()` method works correctly
   - Test `isExpired()` method works correctly
   - Test `isAccepted()` method works correctly
   - Test database foreign keys exist (role_id, invited_by)
   - Test unique constraint on ['email', 'accepted_at']
   - Test timestamps are present and last
3. Run tests: `php artisan test --filter=InvitationTest`

**Verification**: All tests pass

---

### Task 3.3: Add Sent Invitations to User Model
**File**: `app/Models/User.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Add `sentInvitations()` relationship to User model (HasMany Invitation, foreign key 'invited_by')
2. Update UserTest to include test for `sentInvitations()` relationship

**Verification**: Can query `$user->sentInvitations`

---

## Phase 3: Middleware

### Task 3.1: Create AdminMiddleware
**File**: `app/Http/Middleware/AdminMiddleware.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Create file manually (Laravel 12 doesn't have `make:middleware`)
2. Implement `handle()` method:
   - Check if `$request->user()?->isAdmin()` is true
   - If false, `abort(403, 'Unauthorized access.')`
   - If true, return `$next($request)`
3. Make class `final`

**Verification**: Middleware exists, no syntax errors

---

### Task 3.2: Register AdminMiddleware
**File**: `bootstrap/app.php`
**Estimated Time**: 5 minutes

**Steps**:
1. Open `bootstrap/app.php`
2. Add middleware alias in `withMiddleware()`:
   ```php
   $middleware->alias([
       'admin' => \App\Http\Middleware\AdminMiddleware::class,
   ]);
   ```

**Verification**: Middleware registered, can be used in routes

---

## Phase 4: Actions

### Task 4.1: Create CreateInvitation Action
**File**: `app/Actions/CreateInvitation.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:class Actions/CreateInvitation --no-interaction`
2. Add `handle()` method with parameters: `string $email, int $roleId, int $invitedBy`
3. Create invitation record
4. Send email using `Mail::to($invitation->email)->send(new UserInvitationMail($invitation))`
5. Return created invitation
6. Make class `final`

**Verification**: Can call action, invitation created, email queued

---

### Task 4.2: Create AcceptInvitation Action
**File**: `app/Actions/AcceptInvitation.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:class Actions/AcceptInvitation --no-interaction`
2. Add `handle()` method with parameters: `Invitation $invitation, string $name, string $password`
3. Create user with email from invitation, role_id, email_verified_at = now()
4. Update invitation: set `accepted_at` to now()
5. Log user in: `Auth::login($user)`
6. Return created user
7. Make class `final`

**Verification**: Can accept invitation, user created, logged in automatically

---

### Task 4.3: Create ResendInvitation Action
**File**: `app/Actions/ResendInvitation.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Run `php artisan make:class Actions/ResendInvitation --no-interaction`
2. Add `handle()` method with parameter: `Invitation $invitation`
3. Update invitation: set `expires_at` to now()->addDays(7)
4. Send email using `Mail::to($invitation->email)->send(new UserInvitationMail($invitation))`
5. Return updated invitation
6. Make class `final`

**Verification**: Can resend, expiration extended, email sent

---

### Task 4.4: Create ActivateUser Action
**File**: `app/Actions/ActivateUser.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:class Actions/ActivateUser --no-interaction`
2. Add `handle()` method with parameter: `User $user`
3. Update user: set `is_active` to true
4. Return updated user
5. Make class `final`

**Verification**: Can activate user, is_active becomes true

---

### Task 4.5: Create DeactivateUser Action
**File**: `app/Actions/DeactivateUser.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:class Actions/DeactivateUser --no-interaction`
2. Add `handle()` method with parameter: `User $user`
3. Update user: set `is_active` to false
4. Return updated user
5. Make class `final`

**Verification**: Can deactivate user, is_active becomes false

---

### Task 4.6: Create UpdateUserRole Action
**File**: `app/Actions/UpdateUserRole.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:class Actions/UpdateUserRole --no-interaction`
2. Add `handle()` method with parameters: `User $user, int $roleId`
3. Update user: set `role_id` to $roleId
4. Return updated user
5. Make class `final`

**Verification**: Can update role, user role_id changes

---

## Phase 5: Queries

### Task 5.1: Create UsersQuery
**File**: `app/Queries/UsersQuery.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:class Queries/UsersQuery --no-interaction`
2. Add `builder()` method returning Eloquent Builder
3. Query: `User::query()->with('role')->orderBy('created_at', 'desc')`
4. Make class `final`

**Verification**: Can call `$query->builder()->get()`, returns users with roles

---

### Task 5.2: Create PendingInvitationsQuery
**File**: `app/Queries/PendingInvitationsQuery.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Run `php artisan make:class Queries/PendingInvitationsQuery --no-interaction`
2. Add `builder()` method returning Eloquent Builder
3. Query: `Invitation::query()->with(['role', 'inviter'])->whereNull('accepted_at')->where('expires_at', '>', now())->orderBy('created_at', 'desc')`
4. Make class `final`

**Verification**: Can call `$query->builder()->get()`, returns pending invitations

---

### Task 5.3: Create AllRolesQuery
**File**: `app/Queries/AllRolesQuery.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:class Queries/AllRolesQuery --no-interaction`
2. Add `builder()` method returning Eloquent Builder
3. Query: `Role::query()`
4. Make class `final`

**Verification**: Can call `$query->builder()->get()`, returns all roles

---

## Phase 6: Form Requests

### Task 6.1: Create InviteUserRequest
**File**: `app/Http/Requests/InviteUserRequest.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Run `php artisan make:request InviteUserRequest --no-interaction`
2. Set `authorize()` to return `$this->user()->isAdmin()`
3. Define `rules()`:
   - `email`: required, email, max:255, unique:users,email, unique:invitations,email whereNull('accepted_at')
   - `role_id`: required, exists:roles,id
4. Define `messages()`:
   - `email.unique`: 'This email is already registered or has a pending invitation.'
5. Make class `final`

**Verification**: Validation works, duplicate emails rejected

---

### Task 6.2: Create UpdateUserRoleRequest
**File**: `app/Http/Requests/UpdateUserRoleRequest.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:request UpdateUserRoleRequest --no-interaction`
2. Set `authorize()` to return `$this->user()->isAdmin()`
3. Define `rules()`:
   - `role_id`: required, exists:roles,id
4. Make class `final`

**Verification**: Validation works, invalid role_id rejected

---

### Task 6.3: Create AcceptInvitationRequest
**File**: `app/Http/Requests/AcceptInvitationRequest.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:request AcceptInvitationRequest --no-interaction`
2. Set `authorize()` to return `true`
3. Define `rules()`:
   - `name`: required, string, max:255
   - `password`: required, confirmed, Password::defaults()
4. Make class `final`

**Verification**: Validation works, password confirmation required

---

## Phase 7: Controllers

### Task 7.1: Create UserController
**File**: `app/Http/Controllers/UserController.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:controller UserController --no-interaction`
2. Add `index()` method:
   - Inject `UsersQuery`, `PendingInvitationsQuery`, `AllRolesQuery`
   - Call `$usersQuery->builder()->paginate(15)`
   - Call `$pendingInvitationsQuery->builder()->get()`
   - Call `$allRolesQuery->builder()->get()`
   - Return `Inertia::render('Users/Index', [...])`
3. Make class `final`

**Verification**: Route returns Inertia response with correct data

---

### Task 7.2: Create InvitationController
**File**: `app/Http/Controllers/InvitationController.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:controller InvitationController --no-interaction`
2. Add `store()` method:
   - Inject `InviteUserRequest`, `CreateInvitation`
   - Extract email: `$request->string('email')->toString()`
   - Extract role_id: `$request->integer('role_id')`
   - Call action: `$createInvitation->handle($email, $roleId, $request->user()->id)`
   - Return redirect with success message
3. Add `destroy()` method:
   - Inject `Invitation` (route model binding)
   - Delete invitation
   - Return redirect with success message
4. Make class `final`

**Verification**: Can create and delete invitations via routes

---

### Task 7.3: Create ResendInvitationController
**File**: `app/Http/Controllers/ResendInvitationController.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Run `php artisan make:controller ResendInvitationController --invokable --no-interaction`
2. Implement `__invoke()` method:
   - Inject `Invitation`, `ResendInvitation`
   - Check if invitation is accepted, return error if true
   - Call action: `$resendInvitation->handle($invitation)`
   - Return redirect with success message
3. Make class `final`

**Verification**: Can resend invitation via route

---

### Task 7.4: Create ActivateUserController
**File**: `app/Http/Controllers/ActivateUserController.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:controller ActivateUserController --invokable --no-interaction`
2. Implement `__invoke()` method:
   - Inject `User`, `ActivateUser`
   - Call action: `$activateUser->handle($user)`
   - Return redirect with success message
3. Make class `final`

**Verification**: Can activate user via route

---

### Task 7.5: Create DeactivateUserController
**File**: `app/Http/Controllers/DeactivateUserController.php`
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan make:controller DeactivateUserController --invokable --no-interaction`
2. Implement `__invoke()` method:
   - Inject `User`, `DeactivateUser`
   - Call action: `$deactivateUser->handle($user)`
   - Return redirect with success message
3. Make class `final`

**Verification**: Can deactivate user via route

---

### Task 7.6: Create UpdateUserRoleController
**File**: `app/Http/Controllers/UpdateUserRoleController.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Run `php artisan make:controller UpdateUserRoleController --invokable --no-interaction`
2. Implement `__invoke()` method:
   - Inject `UpdateUserRoleRequest`, `User`, `UpdateUserRole`
   - Extract role_id: `$request->integer('role_id')`
   - Call action: `$updateUserRole->handle($user, $roleId)`
   - Return redirect with success message
3. Make class `final`

**Verification**: Can update user role via route

---

### Task 7.7: Create AcceptInvitationController
**File**: `app/Http/Controllers/AcceptInvitationController.php`
**Estimated Time**: 25 minutes

**Steps**:
1. Run `php artisan make:controller AcceptInvitationController --no-interaction`
2. Add `show()` method:
   - Accept `string $token` parameter
   - Query invitation by token, whereNull('accepted_at')
   - Check if expired, abort(410) if true
   - Return `Inertia::render('AcceptInvitation', [...])`
3. Add `store()` method:
   - Inject `AcceptInvitationRequest`, `AcceptInvitation`
   - Accept `string $token` parameter
   - Query invitation by token
   - Check if expired, return redirect with error
   - Extract name: `$request->string('name')->toString()`
   - Extract password: `$request->string('password')->toString()`
   - Call action: `$acceptInvitation->handle($invitation, $name, $password)`
   - Return redirect to dashboard with success message
4. Make class `final`

**Verification**: Can view invitation form, can accept invitation

---

## Phase 8: Routes

### Task 8.1: Add Admin Routes
**File**: `routes/web.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Import all controllers at top of file
2. Add admin routes group with `['auth', 'admin']` middleware and `admin.` prefix
3. Define routes:
   - GET `/admin/users` -> UserController@index (name: admin.users.index)
   - POST `/admin/invitations` -> InvitationController@store (name: admin.invitations.store)
   - POST `/admin/invitations/{invitation}/resend` -> ResendInvitationController (name: admin.invitations.resend)
   - DELETE `/admin/invitations/{invitation}` -> InvitationController@destroy (name: admin.invitations.destroy)
   - POST `/admin/users/{user}/activate` -> ActivateUserController (name: admin.users.activate)
   - POST `/admin/users/{user}/deactivate` -> DeactivateUserController (name: admin.users.deactivate)
   - PATCH `/admin/users/{user}/role` -> UpdateUserRoleController (name: admin.users.role.update)

**Verification**: Run `php artisan route:list`, all routes present

---

### Task 8.2: Add Public Invitation Routes
**File**: `routes/web.php`
**Estimated Time**: 5 minutes

**Steps**:
1. Add guest middleware group
2. Define routes:
   - GET `/invitation/{token}` -> AcceptInvitationController@show (name: invitation.show)
   - POST `/invitation/{token}` -> AcceptInvitationController@store (name: invitation.accept)

**Verification**: Run `php artisan route:list`, routes present

---

## Phase 9: Email

### Task 9.1: Create UserInvitationMail
**File**: `app/Mail/UserInvitationMail.php`
**Estimated Time**: 20 minutes

**Steps**:
1. Run `php artisan make:mail UserInvitationMail --no-interaction`
2. Add constructor with `public Invitation $invitation` (property promotion)
3. Implement `envelope()`: return subject 'You have been invited to PeopleDear'
4. Implement `content()`:
   - Use markdown view 'emails.invitation'
   - Pass data: url, inviterName, roleName, expiresAt
5. Make class `final`

**Verification**: Mailable can be constructed

---

### Task 9.2: Create Invitation Email Template
**File**: `resources/views/emails/invitation.blade.php`
**Estimated Time**: 15 minutes

**Steps**:
1. Create directory: `resources/views/emails/`
2. Create `invitation.blade.php` file
3. Use `<x-mail::message>` component
4. Add heading, body text, button with invitation URL, expiration notice
5. Use variables: `$inviterName`, `$roleName`, `$expiresAt`, `$url`

**Verification**: Email renders correctly when previewed

---

## Phase 10: Frontend - Users Index Page

### Task 10.1: Create Users Index Page
**File**: `resources/js/Pages/Users/Index.vue`
**Estimated Time**: 45 minutes

**Steps**:
1. Create directory: `resources/js/Pages/Users/`
2. Create `Index.vue` with script setup
3. Define props: `users`, `pendingInvitations`, `roles`
4. Use `AppLayout` component
5. Add page heading "User Management"
6. Add "Invite User" button (opens modal)
7. Add search input for filtering users
8. Include `UserTable` component with users data
9. Include `InvitationsTable` component with invitations data
10. Include `InviteUserModal` component (ref for opening)

**Verification**: Page renders, shows users and invitations tables

---

### Task 10.2: Create InviteUserModal Component
**File**: `resources/js/Components/InviteUserModal.vue`
**Estimated Time**: 30 minutes

**Steps**:
1. Create `InviteUserModal.vue` with script setup
2. Use `UModal` component
3. Add form with:
   - `UInput` for email
   - `USelect` for role selection
   - `UButton` for submit
4. Handle form submission using Inertia `router.post`
5. Add validation error display using `UAlert`
6. Add loading state during submission
7. Close modal on success

**Verification**: Can open modal, fill form, submit invitation

---

### Task 10.3: Create UserTable Component
**File**: `resources/js/Components/UserTable.vue`
**Estimated Time**: 45 minutes

**Steps**:
1. Create `UserTable.vue` with script setup
2. Accept `users` prop (paginated data)
3. Use `UTable` component with columns:
   - Name
   - Email
   - Role (using `RoleBadge`)
   - Status (using `UserStatusBadge`)
   - Created Date
   - Actions (dropdown)
4. Add `UDropdown` for each row with actions:
   - Change Role
   - Activate/Deactivate
5. Handle actions using Inertia router
6. Add pagination controls

**Verification**: Table displays users, actions work correctly

---

### Task 10.4: Create InvitationsTable Component
**File**: `resources/js/Components/InvitationsTable.vue`
**Estimated Time**: 30 minutes

**Steps**:
1. Create `InvitationsTable.vue` with script setup
2. Accept `invitations` prop
3. Use `UTable` component with columns:
   - Email
   - Role (using `RoleBadge`)
   - Invited By
   - Sent Date
   - Actions (Resend, Revoke)
4. Add `UButton` components for actions
5. Handle actions using Inertia router
6. Add confirmation modal for revoke action

**Verification**: Table displays invitations, actions work

---

### Task 10.5: Create UserStatusBadge Component
**File**: `resources/js/Components/UserStatusBadge.vue`
**Estimated Time**: 15 minutes

**Steps**:
1. Create `UserStatusBadge.vue` with script setup
2. Accept `isActive` prop (boolean)
3. Use `UBadge` component
4. Show "Active" (green) if true, "Inactive" (red) if false

**Verification**: Badge displays correct status and color

---

### Task 10.6: Create RoleBadge Component
**File**: `resources/js/Components/RoleBadge.vue`
**Estimated Time**: 15 minutes

**Steps**:
1. Create `RoleBadge.vue` with script setup
2. Accept `role` prop (role name string)
3. Use `UBadge` component
4. Set color based on role:
   - Admin: blue
   - Manager: purple
   - Employee: gray
5. Display role display_name

**Verification**: Badge displays correct role with appropriate color

---

## Phase 11: Frontend - Accept Invitation Page

### Task 11.1: Create AcceptInvitation Page
**File**: `resources/js/Pages/AcceptInvitation.vue`
**Estimated Time**: 40 minutes

**Steps**:
1. Create `AcceptInvitation.vue` with script setup
2. Define props: `invitation` (email, role, token)
3. Use `AuthLayout` component (similar to login page)
4. Use `UCard` to contain the form
5. Display invitation details (email, role as badge)
6. Add form with:
   - `UInput` for name
   - `UInput` for password (type="password")
   - `UInput` for password confirmation (type="password")
   - `UButton` for submit
7. Handle form submission using Inertia `router.post`
8. Add validation error display using `UAlert`
9. Add loading state during submission

**Verification**: Page displays invitation details, form works, redirects after success

---

## Phase 12: Navigation

### Task 12.1: Add Users Link to Admin Navigation
**File**: `resources/js/Layouts/AppLayout.vue`
**Estimated Time**: 10 minutes

**Steps**:
1. Open `AppLayout.vue`
2. Find navigation menu section
3. Add "Users" link:
   - Only show if user is admin
   - Route to `route('admin.users.index')`
   - Add appropriate icon
4. Style to match existing nav links

**Verification**: Admin users see Users link, clicking navigates to users page

---

## Phase 13: Testing - Feature Tests

### Task 13.1: Create UserControllerTest
**File**: `tests/Feature/Http/Controllers/UserControllerTest.php`
**Estimated Time**: 45 minutes

**Steps**:
1. Run `php artisan make:test Http/Controllers/UserControllerTest --pest --no-interaction`
2. Write tests:
   - Admin can view users index page
   - Non-admin cannot access users page (403)
   - Users page displays paginated users
   - Users page displays pending invitations
   - Users page displays available roles
3. Use `RefreshDatabase` trait
4. Use factories for test data

**Verification**: Run `php artisan test --filter=UserControllerTest`, all pass

---

### Task 13.2: Create InvitationControllerTest
**File**: `tests/Feature/Http/Controllers/InvitationControllerTest.php`
**Estimated Time**: 60 minutes

**Steps**:
1. Run `php artisan make:test Http/Controllers/InvitationControllerTest --pest --no-interaction`
2. Write tests:
   - Admin can send invitation with valid data
   - Invitation email is sent
   - Cannot send duplicate invitations
   - Admin can revoke invitation
   - Non-admin cannot send invitations (403)
3. Use `Mail::fake()` to test emails
4. Use factories and test datasets

**Verification**: Run `php artisan test --filter=InvitationControllerTest`, all pass

---

### Task 13.3: Create AcceptInvitationControllerTest
**File**: `tests/Feature/Http/Controllers/AcceptInvitationControllerTest.php`
**Estimated Time**: 60 minutes

**Steps**:
1. Run `php artisan make:test Http/Controllers/AcceptInvitationControllerTest --pest --no-interaction`
2. Write tests:
   - Valid invitation token displays registration page
   - Expired invitation shows 410 error
   - Accepted invitation cannot be used again
   - User can accept invitation and create account
   - User is logged in after accepting invitation
   - Invalid token shows 404 error
3. Use `RefreshDatabase` trait

**Verification**: Run `php artisan test --filter=AcceptInvitationControllerTest`, all pass

---

### Task 13.4: Create ResendInvitationControllerTest
**File**: `tests/Feature/Http/Controllers/ResendInvitationControllerTest.php`
**Estimated Time**: 30 minutes

**Steps**:
1. Run `php artisan make:test Http/Controllers/ResendInvitationControllerTest --pest --no-interaction`
2. Write tests:
   - Admin can resend invitation
   - Invitation expiration is updated on resend
   - Email is sent on resend
   - Cannot resend accepted invitation
3. Use `Mail::fake()`

**Verification**: Run tests, all pass

---

### Task 13.5: Create User Activation/Deactivation Tests
**File**: `tests/Feature/Http/Controllers/ActivateUserControllerTest.php` and `DeactivateUserControllerTest.php`
**Estimated Time**: 30 minutes

**Steps**:
1. Create tests for both controllers
2. Write tests:
   - Admin can activate user
   - Admin can deactivate user
   - User status changes correctly
   - Non-admin cannot activate/deactivate (403)

**Verification**: Run tests, all pass

---

### Task 13.6: Create UpdateUserRoleControllerTest
**File**: `tests/Feature/Http/Controllers/UpdateUserRoleControllerTest.php`
**Estimated Time**: 30 minutes

**Steps**:
1. Create test file
2. Write tests:
   - Admin can update user role
   - User role changes correctly
   - Invalid role_id is rejected
   - Non-admin cannot update roles (403)

**Verification**: Run tests, all pass

---

## Phase 14: Testing - Browser Tests

### Task 14.1: Create AdminUsersTest
**File**: `tests/Browser/AdminUsersTest.php`
**Estimated Time**: 60 minutes

**Steps**:
1. Run `php artisan make:test Browser/AdminUsersTest --pest --no-interaction`
2. Write browser tests:
   - Admin can navigate to users page
   - Admin can open invite modal
   - Admin can fill form and send invitation
   - Admin can search/filter users
   - Admin can deactivate user
   - Admin can change user role
3. Use `visit()` for browser testing
4. Assert no JavaScript errors

**Verification**: Run browser tests, all pass

---

### Task 14.2: Create AcceptInvitationTest
**File**: `tests/Browser/AcceptInvitationTest.php`
**Estimated Time**: 45 minutes

**Steps**:
1. Run `php artisan make:test Browser/AcceptInvitationTest --pest --no-interaction`
2. Write browser tests:
   - User can access invitation link
   - User can fill registration form
   - User can create account
   - User is redirected to dashboard after registration
   - Validation errors are displayed correctly
3. Use `visit()` and form interactions

**Verification**: Run browser tests, all pass

---

## Phase 15: Code Quality

### Task 15.1: Run Laravel Pint
**Estimated Time**: 5 minutes

**Steps**:
1. Run `vendor/bin/pint --dirty`
2. Review changes
3. Commit formatted code

**Verification**: No style issues remain

---

### Task 15.2: Run Larastan
**Estimated Time**: 15 minutes

**Steps**:
1. Run `vendor/bin/phpstan analyse` or `./vendor/bin/phpstan analyse`
2. Fix any level 8 static analysis errors
3. Re-run until clean

**Verification**: Larastan passes with no errors

---

### Task 15.3: Run All Tests
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan test`
2. Verify all feature and unit tests pass
3. Run browser tests separately if needed

**Verification**: All tests pass (green)

---

## Phase 16: Documentation

### Task 16.1: Create Developer Documentation
**File**: `docs/features/user-management.md`
**Estimated Time**: 60 minutes

**Steps**:
1. Create `docs/features/` directory
2. Write comprehensive developer documentation covering:
   - Architecture overview
   - Database schema
   - Authentication flow
   - Invitation flow
   - Testing strategy
3. Include code examples

**Verification**: Documentation is clear and complete

---

### Task 16.2: Create Admin User Guide
**File**: `docs/guides/admin-user-management.md`
**Estimated Time**: 45 minutes

**Steps**:
1. Create `docs/guides/` directory
2. Write user-friendly guide for administrators
3. Include screenshots (to be added)
4. Cover all admin operations

**Verification**: Guide is easy to follow

---

### Task 16.3: Create User Invitation Guide
**File**: `docs/guides/accepting-invitation.md`
**Estimated Time**: 30 minutes

**Steps**:
1. Write guide for invited users
2. Explain registration process step-by-step
3. Include troubleshooting section

**Verification**: Guide is clear for non-technical users

---

### Task 16.4: Update README.md
**File**: `README.md`
**Estimated Time**: 20 minutes

**Steps**:
1. Add "User Management Setup" section
2. Document migration and seeding steps
3. Document creating first admin user
4. Document email configuration

**Verification**: Setup instructions are complete

---

### Task 16.5: Update Product Roadmap
**File**: `agent-os/product/roadmap.md`
**Estimated Time**: 10 minutes

**Steps**:
1. Mark Phase 1.1 as completed
2. Update success criteria status
3. Add completion date

**Verification**: Roadmap reflects completed work

---

## Phase 17: Deployment Preparation

### Task 17.1: Create First Admin User
**Estimated Time**: 10 minutes

**Steps**:
1. Run `php artisan tinker`
2. Create admin user with code from spec
3. Verify admin can log in
4. Verify admin can access users page

**Verification**: Admin user exists and functional

---

### Task 17.2: Configure Mailgun
**Estimated Time**: 15 minutes

**Steps**:
1. Add Mailgun credentials to `.env`
2. Test email sending via tinker
3. Verify emails are delivered

**Verification**: Emails send successfully

---

### Task 17.3: Final Testing in Development
**Estimated Time**: 30 minutes

**Steps**:
1. Run through complete user flow:
   - Admin logs in
   - Admin invites user
   - Check email received
   - Accept invitation
   - Verify new user logged in
2. Test all CRUD operations
3. Test edge cases

**Verification**: All flows work end-to-end

---

## Summary

**Total Estimated Time**: ~18-20 hours

**Task Distribution**:
- Database (4 tasks): ~45 minutes
- Models (3 tasks): ~65 minutes
- Middleware (2 tasks): ~20 minutes
- Actions (6 tasks): ~105 minutes
- Queries (3 tasks): ~35 minutes
- Form Requests (3 tasks): ~35 minutes
- Controllers (7 tasks): ~130 minutes
- Routes (2 tasks): ~20 minutes
- Email (2 tasks): ~35 minutes
- Frontend Pages (6 tasks): ~160 minutes
- Navigation (1 task): ~10 minutes
- Feature Tests (6 tasks): ~255 minutes
- Browser Tests (2 tasks): ~105 minutes
- Code Quality (3 tasks): ~30 minutes
- Documentation (5 tasks): ~165 minutes
- Deployment (3 tasks): ~55 minutes

**Dependencies**:
- Phases must be completed in order
- Testing can be done incrementally alongside development
- Documentation can be written in parallel with implementation

**Success Criteria**:
- All 70+ tasks completed
- All tests passing
- Code quality checks pass
- Documentation complete
- Feature deployed and functional

---

**Last Updated**: October 15, 2025