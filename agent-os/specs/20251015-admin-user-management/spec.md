# Admin User Management & Invitations

**Feature**: Admin User Management Page with User Invitation System
**Priority**: Critical (Phase 1.1)
**Status**: Specification
**Created**: October 15, 2025
**Visual Reference**: `planning/visuals/img.png`

---

## Visual Design Reference

This feature follows the existing **Profile page pattern** (`resources/js/Pages/Profile/`):
- Settings page with sidebar navigation (like Profile/Layout.vue)
- Clean card-based layout (like Profile/General.vue)
- Inline invitation form (NOT a modal)
- Member cards instead of tables

**Key Components Reused**:
- `UDashboardPanel` - Main container
- `UNavigationMenu` - Sidebar navigation
- `UPageCard` - Content sections
- `UFormField` - Form inputs

See `planning/visuals/img.png` for the target design.

---

## Overview

The admin user management page allows administrators to view all users, invite new users via email, manage user roles, and control access to the PeopleDear platform. This is the foundation for the role-based access control system.

---

## User Stories

### As an Administrator
- I want to view a list of all users in the system
- I want to invite new users by email so they can register
- I want to assign roles to users (Admin, Manager, Employee)
- I want to see the status of invitations (pending, accepted, expired)
- I want to resend or revoke invitations
- I want to deactivate users without deleting their data
- I want to search and filter users by name, email, role, or status

### As an Invited User
- I want to receive an email invitation with a registration link
- I want to create my account by setting my name and password
- I want the registration process to be simple and secure
- I want to know which company I'm joining

---

## Database Schema

### Migration: `create_roles_table`

```php
Schema::create('roles', function (Blueprint $table): void {
    $table->id();
    $table->string('name')->unique(); // 'admin', 'manager', 'employee'
    $table->string('display_name'); // 'Administrator', 'Manager', 'Employee'
    $table->text('description')->nullable();
    $table->timestamps();
});
```

**Seed Data**:
- `admin`: Administrator - Full system access
- `manager`: Manager - Can approve requests and view team data
- `employee`: Employee - Can submit requests and view own data

---

### Migration: `add_role_to_users_table`

```php
Schema::table('users', function (Blueprint $table): void {
    $table->foreignId('role_id')
        ->nullable()
        ->after('avatar')
        ->constrained('roles')
        ->nullOnDelete();

    $table->boolean('is_active')->default(true)->after('role_id');
});
```

---

### Migration: `create_invitations_table`

```php
Schema::create('invitations', function (Blueprint $table): void {
    $table->id();
    $table->string('email')->index();
    $table->string('token')->unique();
    $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
    $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
    $table->timestamp('accepted_at')->nullable();
    $table->timestamp('expires_at');
    $table->timestamps();

    // Prevent duplicate pending invitations for the same email
    $table->unique(['email', 'accepted_at']);
});
```

**Fields**:
- `email`: Email address of the invited user
- `token`: Unique token for the invitation link (UUID)
- `role_id`: Role assigned to the user upon registration
- `invited_by`: Admin who sent the invitation
- `accepted_at`: Timestamp when the invitation was accepted (NULL = pending)
- `expires_at`: Invitation expiration (default: 7 days from creation)

---

## Models

### `Role` Model

**Location**: `app/Models/Role.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $display_name
 * @property-read string|null $description
 */
final class Role extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
```

---

### `Invitation` Model

**Location**: `app/Models/Invitation.php`

```php
<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $token
 * @property-read int $role_id
 * @property-read int $invited_by
 * @property-read CarbonInterface|null $accepted_at
 * @property-read CarbonInterface $expires_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read Role $role
 * @property-read User $inviter
 */
final class Invitation extends Model
{
    public function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            if (!$invitation->token) {
                $invitation->token = Str::uuid()->toString();
            }

            if (!$invitation->expires_at) {
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }
}
```

---

### Update `User` Model

**Location**: `app/Models/User.php`

Add relationship to Role and Invitations:

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

public function role(): BelongsTo
{
    return $this->belongsTo(Role::class);
}

public function sentInvitations(): HasMany
{
    return $this->hasMany(Invitation::class, 'invited_by');
}

public function isAdmin(): bool
{
    return $this->role?->name === 'admin';
}

public function isManager(): bool
{
    return $this->role?->name === 'manager';
}

public function isEmployee(): bool
{
    return $this->role?->name === 'employee';
}
```

---

## Routes

**Location**: `routes/web.php`

```php
use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\ActivateUserController;
use App\Http\Controllers\DeactivateUserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ResendInvitationController;
use App\Http\Controllers\UpdateUserRoleController;
use App\Http\Controllers\UserController;

// Admin routes (protected by 'auth' and 'admin' middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function (): void {

    // User management
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    // Invitation management
    Route::post('/invitations', [InvitationController::class, 'store'])
        ->name('invitations.store');

    Route::post('/invitations/{invitation}/resend', ResendInvitationController::class)
        ->name('invitations.resend');

    Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])
        ->name('invitations.destroy');

    // User activation/deactivation
    Route::post('/users/{user}/activate', ActivateUserController::class)
        ->name('users.activate');

    Route::post('/users/{user}/deactivate', DeactivateUserController::class)
        ->name('users.deactivate');

    // Role management
    Route::patch('/users/{user}/role', UpdateUserRoleController::class)
        ->name('users.role.update');
});

// Public invitation acceptance routes
Route::middleware(['guest'])->group(function (): void {
    Route::get('/invitation/{token}', [AcceptInvitationController::class, 'show'])
        ->name('invitation.show');

    Route::post('/invitation/{token}', [AcceptInvitationController::class, 'store'])
        ->name('invitation.accept');
});
```

---

## Middleware

### `AdminMiddleware`

**Location**: `app/Http/Middleware/AdminMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

**Register in**: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

---

## Actions

### `CreateInvitation`

**Location**: `app/Actions/CreateInvitation.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;

final class CreateInvitation
{
    public function handle(string $email, int $roleId, int $invitedBy): Invitation
    {
        $invitation = Invitation::create([
            'email' => $email,
            'role_id' => $roleId,
            'invited_by' => $invitedBy,
        ]);

        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        return $invitation;
    }
}
```

---

### `AcceptInvitation`

**Location**: `app/Actions/AcceptInvitation.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class AcceptInvitation
{
    public function handle(Invitation $invitation, string $name, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $invitation->email,
            'password' => $password,
            'role_id' => $invitation->role_id,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return $user;
    }
}
```

---

### `ResendInvitation`

**Location**: `app/Actions/ResendInvitation.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;

final class ResendInvitation
{
    public function handle(Invitation $invitation): Invitation
    {
        $invitation->update(['expires_at' => now()->addDays(7)]);

        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        return $invitation;
    }
}
```

---

### `ActivateUser`

**Location**: `app/Actions/ActivateUser.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class ActivateUser
{
    public function handle(User $user): User
    {
        $user->update(['is_active' => true]);

        return $user;
    }
}
```

---

### `DeactivateUser`

**Location**: `app/Actions/DeactivateUser.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class DeactivateUser
{
    public function handle(User $user): User
    {
        $user->update(['is_active' => false]);

        return $user;
    }
}
```

---

### `UpdateUserRole`

**Location**: `app/Actions/UpdateUserRole.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class UpdateUserRole
{
    public function handle(User $user, int $roleId): User
    {
        $user->update(['role_id' => $roleId]);

        return $user;
    }
}
```

---

## Queries

### `UsersQuery`

**Location**: `app/Queries/UsersQuery.php`

```php
<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class UsersQuery
{
    public function builder(): Builder
    {
        return User::query()
            ->with('role')
            ->orderBy('created_at', 'desc');
    }
}
```

---

### `PendingInvitationsQuery`

**Location**: `app/Queries/PendingInvitationsQuery.php`

```php
<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Builder;

final class PendingInvitationsQuery
{
    public function builder(): Builder
    {
        return Invitation::query()
            ->with(['role', 'inviter'])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc');
    }
}
```

---

### `AllRolesQuery`

**Location**: `app/Queries/AllRolesQuery.php`

```php
<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

final class AllRolesQuery
{
    public function builder(): Builder
    {
        return Role::query();
    }
}
```

---

## Controllers

### `UserController`

**Location**: `app/Http/Controllers/UserController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\AllRolesQuery;
use App\Queries\PendingInvitationsQuery;
use App\Queries\UsersQuery;
use Inertia\Inertia;
use Inertia\Response;

final class UserController
{
    public function index(
        UsersQuery $usersQuery,
        PendingInvitationsQuery $pendingInvitationsQuery,
        AllRolesQuery $allRolesQuery
    ): Response {
        $users = $usersQuery->builder()->paginate(15);

        $pendingInvitations = $pendingInvitationsQuery->builder()->get();

        $roles = $allRolesQuery->builder()->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'pendingInvitations' => $pendingInvitations,
            'roles' => $roles,
        ]);
    }
}
```

---

### `InvitationController`

**Location**: `app/Http/Controllers/InvitationController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateInvitation;
use App\Http\Requests\InviteUserRequest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

final class InvitationController
{
    public function store(InviteUserRequest $request, CreateInvitation $createInvitation): RedirectResponse
    {
        $createInvitation->handle(
            email: $request->string('email')->toString(),
            roleId: $request->integer('role_id'),
            invitedBy: $request->user()->id,
        );

        return redirect()->back()->with('success', 'Invitation sent successfully.');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return redirect()->back()->with('success', 'Invitation revoked successfully.');
    }
}
```

---

### `ResendInvitationController` (Single Action)

**Location**: `app/Http/Controllers/ResendInvitationController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ResendInvitation;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

final class ResendInvitationController
{
    public function __invoke(Invitation $invitation, ResendInvitation $resendInvitation): RedirectResponse
    {
        if ($invitation->isAccepted()) {
            return redirect()->back()->withErrors(['invitation' => 'This invitation has already been accepted.']);
        }

        $resendInvitation->handle($invitation);

        return redirect()->back()->with('success', 'Invitation resent successfully.');
    }
}
```

---

### `ActivateUserController` (Single Action)

**Location**: `app/Http/Controllers/ActivateUserController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ActivateUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class ActivateUserController
{
    public function __invoke(User $user, ActivateUser $activateUser): RedirectResponse
    {
        $activateUser->handle($user);

        return redirect()->back()->with('success', 'User activated successfully.');
    }
}
```

---

### `DeactivateUserController` (Single Action)

**Location**: `app/Http/Controllers/DeactivateUserController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeactivateUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class DeactivateUserController
{
    public function __invoke(User $user, DeactivateUser $deactivateUser): RedirectResponse
    {
        $deactivateUser->handle($user);

        return redirect()->back()->with('success', 'User deactivated successfully.');
    }
}
```

---

### `UpdateUserRoleController` (Single Action)

**Location**: `app/Http/Controllers/UpdateUserRoleController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateUserRole;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class UpdateUserRoleController
{
    public function __invoke(UpdateUserRoleRequest $request, User $user, UpdateUserRole $updateUserRole): RedirectResponse
    {
        $updateUserRole->handle($user, $request->integer('role_id'));

        return redirect()->back()->with('success', 'User role updated successfully.');
    }
}
```

---

### `AcceptInvitationController` (Public)

**Location**: `app/Http/Controllers/AcceptInvitationController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AcceptInvitation;
use App\Http\Requests\AcceptInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class AcceptInvitationController
{
    public function show(string $token): Response
    {
        $invitation = Invitation::with('role')
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

    public function store(AcceptInvitationRequest $request, string $token, AcceptInvitation $acceptInvitation): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('auth.login.index')
                ->withErrors(['token' => 'This invitation has expired.']);
        }

        $acceptInvitation->handle(
            invitation: $invitation,
            name: $request->string('name')->toString(),
            password: $request->string('password')->toString(),
        );

        return redirect()->route('dashboard')->with('success', 'Welcome to PeopleDear!');
    }
}
```

---

## Form Requests

### `InviteUserRequest`

**Location**: `app/Http/Requests/InviteUserRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->whereNull('accepted_at'),
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered or has a pending invitation.',
        ];
    }
}
```

---

### `UpdateUserRoleRequest`

**Location**: `app/Http/Requests/UpdateUserRoleRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }
}
```

---

### `AcceptInvitationRequest`

**Location**: `app/Http/Requests/AcceptInvitationRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class AcceptInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }
}
```

---

## Email Notification

### `UserInvitationMail`

**Location**: `app/Mail/UserInvitationMail.php`

```php
<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invitation $invitation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to PeopleDear',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation',
            with: [
                'url' => route('invitation.show', $this->invitation->token),
                'inviterName' => $this->invitation->inviter->name,
                'roleName' => $this->invitation->role->display_name,
                'expiresAt' => $this->invitation->expires_at->format('F j, Y'),
            ],
        );
    }
}
```

---

### Email Template

**Location**: `resources/views/emails/invitation.blade.php`

```blade
<x-mail::message>
# Welcome to PeopleDear!

{{ $inviterName }} has invited you to join PeopleDear as a **{{ $roleName }}**.

Click the button below to accept your invitation and create your account:

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

This invitation will expire on **{{ $expiresAt }}**.

If you have any questions, please contact your administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

---

## Frontend Pages

### Settings Layout (Reuse Existing Pattern)

**Location**: `resources/js/Pages/Settings/Layout.vue`

**Pattern**: Follow the existing `Profile/Layout.vue` pattern exactly
- Uses `UDashboardPanel` with sidebar navigation
- Left sidebar with `UNavigationMenu` (vertical orientation, 64 width)
- Content area on right via `<slot />`

**Navigation Items**:
```typescript
const items = ref([
    {
        label: "General",
        to: "/settings",
    },
    {
        label: "Members",
        to: "/settings/members",
    },
    {
        label: "Roles",
        to: "/settings/roles",
    },
    // ... other settings
]);
```

---

### Members Page

**Location**: `resources/js/Pages/Settings/Members.vue`

**Pattern**: Follow `Profile/General.vue` structure using `UPageCard`

**Features**:
- **Inline invitation form** at top (NOT a modal)
  - Email input field
  - Role dropdown
  - "Send invite" button
  - All in single `UPageCard` with horizontal orientation
- **Members list** below invitation form
  - Card-based layout (NOT table)
  - Each member shows: avatar, name, email, teams, role badge, action menu
  - List all organization members

**Nuxt UI 4 Components to Use**:
- `UPageCard` - Main container sections (invitation, members list)
- `UFormField` - Form fields with labels
- `UInput` - Email input
- `USelect` - Role dropdown
- `UButton` - Send invite button
- `UAvatar` - Member avatars
- `UBadge` - Role badges
- `UDropdown` - Action menus (...)
- `USeparator` - Between members

**Components to create**:
- `MemberCard.vue` - Individual member display (avatar, name, email, role, teams, actions)
- `RoleBadge.vue` - Badge showing user role (uses UBadge)

---

### Accept Invitation Page

**Location**: `resources/js/Pages/AcceptInvitation.vue`

**Features**:
- Display invitation details (email, role)
- Registration form with fields:
  - Name (required)
  - Password (required, min 8 characters)
  - Confirm Password (required, must match)
- Submit button to create account
- Show validation errors
- Redirect to dashboard after successful registration

**Nuxt UI 4 Components to Use**:
- `UCard` - Form container
- `UInput` - Form inputs (name, password, confirm password)
- `UButton` - Submit button
- `UAlert` - Validation errors
- `UBadge` - Role display

---

## UI/UX Requirements

### Admin Users Page
1. **Layout**: Use existing `AppLayout.vue`
2. **Navigation**: Add "Users" link to admin navigation menu
3. **Tables**:
   - Use `UTable` with pagination built-in
   - Sortable columns (name, email, role, created date)
   - Search bar using `UInput` with search icon
4. **Modals**:
   - Use `UModal` for Invite User form with role `USelect`
   - Use `UModal` with confirmation for destructive actions (deactivate, revoke)
5. **Badges**:
   - Use `UBadge` with `color="green"` for "Active" users
   - Use `UBadge` with `color="red"` for "Inactive" users
   - Use `UBadge` with varying colors for roles (Admin/Manager/Employee)
6. **Actions**:
   - Use `UDropdown` for user action menus
   - Use `UButton` with appropriate variants for primary/secondary actions

### Accept Invitation Page
1. **Layout**: Use `AuthLayout.vue` (similar to login page)
2. **Form**:
   - Use `UCard` to contain the form
   - Use `UInput` for all form fields with proper type attributes
   - Show email and role (read-only) using `UBadge`
   - Use `UButton` with loading state for submission
   - Display validation errors using `UAlert` with `color="red"`
3. **Branding**: Match existing auth pages styling

---

## Security Considerations

1. **Invitation Token**: Use UUID for unpredictable tokens
2. **Expiration**: Invitations expire after 7 days
3. **Email Uniqueness**: Prevent duplicate invitations or registrations
4. **Role Protection**: Only admins can send invitations and manage users
5. **Password Requirements**: Enforce strong password policy
6. **Rate Limiting**: Limit invitation sending to prevent abuse
7. **Email Verification**: Mark users as verified upon invitation acceptance

---

## Testing Requirements

### Feature Tests

**Location**: `tests/Feature/Http/Controllers/Admin/UserControllerTest.php`

- Test admin can view users index page
- Test non-admin cannot access admin routes
- Test admin can send invitation with valid data
- Test invitation email is sent
- Test cannot send duplicate invitations
- Test admin can toggle user status
- Test admin can update user role

**Location**: `tests/Feature/Http/Controllers/InvitationControllerTest.php`

- Test valid invitation token displays registration page
- Test expired invitation shows error
- Test accepted invitation cannot be used again
- Test user can accept invitation and create account
- Test admin can resend invitation
- Test admin can revoke invitation
- Test invitation expiration is updated on resend

### Browser Tests

**Location**: `tests/Browser/AdminUsersTest.php`

- Test admin can navigate to users page
- Test admin can open invite modal and send invitation
- Test admin can search/filter users
- Test admin can deactivate user
- Test admin can change user role

**Location**: `tests/Browser/AcceptInvitationTest.php`

- Test user can access invitation link
- Test user can fill registration form and create account
- Test user is redirected to dashboard after registration
- Test validation errors are displayed correctly

---

## Success Criteria

- ✅ Admin can view all users and pending invitations
- ✅ Admin can invite users by email with role assignment
- ✅ Invitation emails are sent successfully via Mailgun
- ✅ Invited users receive email with secure registration link
- ✅ Users can register using invitation link
- ✅ Invitations expire after 7 days
- ✅ Admin can resend or revoke invitations
- ✅ Admin can activate/deactivate users
- ✅ Admin can change user roles
- ✅ All actions are protected by admin middleware
- ✅ All feature and browser tests pass

---

## Implementation Checklist

### Backend
- [ ] Create `roles` table migration and seed data
- [ ] Create `invitations` table migration
- [ ] Update `users` table migration (add `role_id` and `is_active`)
- [ ] Create `Role` model with relationships
- [ ] Create `Invitation` model with business logic methods
- [ ] Update `User` model with role relationships and helper methods
- [ ] Create `AdminMiddleware` and register in `bootstrap/app.php`
- [ ] Create `InviteUserRequest` form request
- [ ] Create `UpdateUserRoleRequest` form request
- [ ] Create `AcceptInvitationRequest` form request
- [ ] Implement `Admin\UserController` methods
- [ ] Implement `InvitationController` methods
- [ ] Create `UserInvitationMail` mailable
- [ ] Create invitation email template
- [ ] Add admin and invitation routes to `routes/web.php`

### Frontend
- [ ] Create `Admin/Users/Index.vue` page
- [ ] Create `InviteUserModal.vue` component
- [ ] Create `UserTable.vue` component
- [ ] Create `InvitationsTable.vue` component
- [ ] Create `UserStatusBadge.vue` component
- [ ] Create `RoleBadge.vue` component
- [ ] Create `Auth/AcceptInvitation.vue` page
- [ ] Add "Users" navigation link to admin menu (in `AppLayout.vue`)

### Testing
- [ ] Write feature tests for `Admin\UserController`
- [ ] Write feature tests for `InvitationController`
- [ ] Write browser tests for admin users page
- [ ] Write browser tests for invitation acceptance flow
- [ ] Run all tests and ensure they pass

### Deployment
- [ ] Run migrations in development
- [ ] Seed roles data
- [ ] Configure Mailgun for email sending
- [ ] Test invitation emails in staging environment
- [ ] Run `vendor/bin/pint` to format code
- [ ] Run Larastan for static analysis

### Documentation
- [ ] Create developer documentation for user management architecture
- [ ] Create user guide for admin user management
- [ ] Create user guide for accepting invitations
- [ ] Update product roadmap marking Phase 1.1 as completed
- [ ] Update CLAUDE.md if new patterns are introduced

---

## Documentation Requirements

### Developer Documentation

**Location**: `docs/features/user-management.md`

Should include:
- **Architecture Overview**: Explanation of Actions, Queries, and Controllers pattern
- **Database Schema**: Tables, relationships, and key fields
- **Authentication Flow**: How admin middleware works
- **Invitation Flow**: Step-by-step process from sending to accepting
- **Role System**: How roles are defined and enforced
- **Email Integration**: Mailgun configuration and email templates
- **Testing Strategy**: How to run and write tests for user management
- **API Endpoints**: All routes with request/response examples
- **Extending the System**: How to add new roles or permissions

**Example Structure**:
```markdown
# User Management System

## Overview
The user management system handles user invitations, role assignment, and access control.

## Architecture

### Controllers
- `UserController` - Displays user management dashboard
- `InvitationController` - Creates and revokes invitations
- `ActivateUserController` - Activates user accounts
- `DeactivateUserController` - Deactivates user accounts
- `UpdateUserRoleController` - Updates user roles
- `AcceptInvitationController` - Handles invitation acceptance

### Actions (Business Logic)
- `CreateInvitation` - Creates invitation and sends email
- `AcceptInvitation` - Creates user from invitation
- `ActivateUser` - Activates user account
- `DeactivateUser` - Deactivates user account
- `UpdateUserRole` - Changes user role

### Queries (Data Retrieval)
- `UsersQuery` - Fetches all users with roles
- `PendingInvitationsQuery` - Fetches pending invitations
- `AllRolesQuery` - Fetches all available roles

## Database Schema
[Include ER diagram or detailed schema documentation]

## API Endpoints
[Document all routes with examples]

## Testing
[How to run tests and write new tests]
```

---

### User Documentation

#### **Admin User Guide**

**Location**: `docs/guides/admin-user-management.md`

Should include:
- **Accessing User Management**: How to navigate to the users page
- **Inviting Users**: Step-by-step guide with screenshots
  - Clicking "Invite User" button
  - Filling out invitation form (email, role selection)
  - Understanding invitation expiration (7 days)
- **Managing Users**:
  - Viewing user list and status
  - Activating/deactivating users
  - Changing user roles
  - Understanding role permissions
- **Managing Invitations**:
  - Viewing pending invitations
  - Resending invitations
  - Revoking invitations
- **Troubleshooting**:
  - What to do if invitation email isn't received
  - How to handle expired invitations
  - Permission denied errors

**Example Structure**:
```markdown
# Admin User Management Guide

## Overview
As an administrator, you can invite new users, assign roles, and manage user access to PeopleDear.

## Inviting New Users

1. Navigate to **Admin > Users** in the sidebar
2. Click the **"Invite User"** button
3. Enter the user's email address
4. Select their role:
   - **Administrator**: Full system access
   - **Manager**: Can approve requests and view team data
   - **Employee**: Can submit requests and view own data
5. Click **"Send Invitation"**

The user will receive an email with a registration link that expires in 7 days.

## Managing Existing Users

### Viewing Users
The users page displays all users with:
- Name and email
- Assigned role
- Status (Active/Inactive)
- Registration date

### Activating/Deactivating Users
1. Find the user in the users list
2. Click the actions menu (⋮) next to their name
3. Select **"Activate"** or **"Deactivate"**

Deactivated users cannot log in but their data is preserved.

### Changing User Roles
1. Find the user in the users list
2. Click the actions menu (⋮) next to their name
3. Select **"Change Role"**
4. Choose the new role
5. Click **"Update"**

## Managing Invitations

### Viewing Pending Invitations
Pending invitations are shown in a separate table below the users list.

### Resending an Invitation
If a user didn't receive their invitation:
1. Find the invitation in the pending invitations table
2. Click **"Resend"**
3. A new invitation email will be sent with an extended expiration date

### Revoking an Invitation
To cancel an invitation:
1. Find the invitation in the pending invitations table
2. Click **"Revoke"**
3. Confirm the action

The invitation link will no longer work.

## Troubleshooting

**User didn't receive invitation email**
- Check spam/junk folders
- Verify the email address is correct
- Resend the invitation

**Invitation expired**
- Send a new invitation to the same email address
- The previous expired invitation will be replaced

**Permission denied when inviting users**
- Only administrators can invite users and manage roles
- Contact your administrator if you need elevated permissions
```

---

#### **User Invitation Acceptance Guide**

**Location**: `docs/guides/accepting-invitation.md`

Should include:
- **Receiving the Invitation**: What the email looks like
- **Accepting the Invitation**: Step-by-step registration process
  - Clicking the invitation link
  - Creating account (name, password)
  - Password requirements
- **First Login**: What to expect after registration
- **Troubleshooting**:
  - Expired invitation links
  - Invalid invitation errors
  - Password requirements not met

**Example Structure**:
```markdown
# Accepting Your Invitation to PeopleDear

## You've Been Invited!

You should have received an email with the subject "You have been invited to PeopleDear" from your administrator.

## Creating Your Account

1. **Open the invitation email**
   - Check your inbox for an email from PeopleDear
   - If you don't see it, check your spam/junk folder

2. **Click "Accept Invitation"**
   - The button will take you to the registration page
   - You'll see your email address and assigned role

3. **Fill in your details**
   - **Full Name**: Enter your name
   - **Password**: Create a strong password (minimum 8 characters)
   - **Confirm Password**: Re-enter your password

4. **Click "Create Account"**
   - You'll be automatically logged in
   - You'll be taken to your dashboard

## Password Requirements

Your password must:
- Be at least 8 characters long
- Contain letters and numbers (recommended)
- Be unique and not easily guessable

## After Registration

Once you've created your account:
- You can immediately start using PeopleDear
- Your role (Admin, Manager, or Employee) determines what you can access
- You can update your profile information in **Profile Settings**

## Troubleshooting

**"This invitation has expired"**
- Invitations are valid for 7 days
- Contact your administrator to request a new invitation

**"Invalid invitation link"**
- The link may have already been used
- The invitation may have been revoked
- Contact your administrator for assistance

**Password doesn't meet requirements**
- Ensure your password is at least 8 characters
- Try including uppercase, lowercase, numbers, and symbols
```

---

### Product Roadmap Update

**Location**: `agent-os/product/roadmap.md`

Update Phase 1.1 section to mark as completed:

```markdown
### 1.1 User & Company Management
**Priority**: Critical
**Timeline**: Week 1-2
**Status**: ✅ Completed

- [x] Company account creation and configuration
- [x] Employee onboarding with role-based access control
- [x] Admin panel for user management
- [x] Approval hierarchy setup (multi-level workflows)
- [x] Permission system (admin, manager, employee roles)

**Success Criteria**: ✅ Met
- Ability to create company with multiple employees
- Role-based permissions working correctly
- Approval chains configurable per company
```

---

### README.md Updates

**Location**: `README.md`

Add setup instructions for user management:

```markdown
## User Management Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Roles
```bash
php artisan db:seed --class=RoleSeeder
```

This creates three roles:
- **Admin**: Full system access
- **Manager**: Can approve requests and view team data
- **Employee**: Can submit requests and view own data

### 3. Create First Admin User
You can create the first admin user via Tinker:

```bash
php artisan tinker
```

```php
$adminRole = \App\Models\Role::where('name', 'admin')->first();

\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => 'your-secure-password',
    'role_id' => $adminRole->id,
    'email_verified_at' => now(),
]);
```

### 4. Configure Email (Mailgun)
Add to your `.env`:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

Test email sending:
```bash
php artisan tinker
```

```php
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```
```

---

**Last Updated**: October 15, 2025