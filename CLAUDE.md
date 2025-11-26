<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Project-Specific Guidelines

This project maintains detailed guidelines in the `.ai/guidelines/` directory that complement these Laravel Boost guidelines:

- **`general.blade.php`** - General coding conventions and PHP annotations
- **`app.actions.blade.php`** - Action pattern and business logic guidelines
- **`app.controllers.blade.php`** - Controller structure, Form Requests, and data flow
- **`app.data.blade.php`** - Data objects as DTOs (not validation layers)
- **`app.models.blade.php`** - Eloquent model structure, type hints, and relationship annotations
- **`database.migrations.blade.php`** - Comprehensive database migration rules
- **`tests.blade.php`** - Testing conventions, container resolution, Pest patterns

These guideline files use the `@boostsnippet` directive for code examples and are automatically included by Laravel Boost.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

### Backend
- php - 8.4.13
- laravel/framework (LARAVEL) - v12
- laravel/fortify (FORTIFY) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- inertiajs/inertia-laravel (INERTIA) - v2
- spatie/laravel-permission (PERMISSION) - v6
- spatie/laravel-settings (SETTINGS) - v3
- spatie/laravel-data (DATA) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- laravel/mcp (MCP) - v0

### Frontend
- react (REACT) - v18
- @inertiajs/react (INERTIA-REACT) - v2
- typescript (TYPESCRIPT) - v5
- tailwindcss (TAILWIND) - v4
- shadcn/ui (SHADCN) - components library
- vite (VITE) - v6
- prettier (PRETTIER) - v3


## Core Conventions
- Follow all existing code conventions - check sibling files for structure, approach, and naming
- Use descriptive names: `isRegisteredForDiscounts` not `discount()`
- **Always chain methods on new lines** for better readability
- Reuse existing components before creating new ones
- Prefer tests over verification scripts or tinker sessions
- Do not create new base folders or change dependencies without approval

## Git Workflow
- **Always create feature branches**: `git checkout -b feature/descriptive-name`
- **Before branching**: `git fetch && git pull origin main`
- **Before every commit**:
  - Run `composer test:unit` (requires 100% coverage)
  - Run `vendor/bin/pint --dirty` (format code)
  - Run `composer test:types` (static analysis)
- Only merge to main after tests pass and code is reviewed

## Composer Scripts

### Key Commands
- **`composer test`** - Full test suite (type coverage, unit tests, linting, PHPStan)
- **`composer test:unit`** - Unit tests with 100% coverage requirement
- **`composer test:types`** - Static analysis (PHPStan + npm types)
- **`composer lint`** - Fix code style (rector, pint, npm lint)
- **`composer dev`** - Start all dev servers (serve, queue, pail, npm dev)

## Communication & Documentation
- Be concise - focus on important details, not obvious ones
- Only create documentation files when explicitly requested
- If frontend changes aren't visible, suggest running `npm run build`, `npm run dev`, or `composer run dev`


=== boost rules ===

## Laravel Boost Tools
Laravel Boost is an MCP server with powerful tools for this application:

- **`search-docs`** - Search version-specific Laravel ecosystem documentation (use FIRST before other approaches)
- **`list-artisan-commands`** - Verify available Artisan command parameters
- **`get-absolute-url`** - Generate correct absolute URLs with proper scheme/domain/port
- **`tinker`** - Execute PHP to debug code or query Eloquent models
- **`database-query`** - Read-only database queries
- **`browser-logs`** - Read recent browser logs, errors, and exceptions

## Documentation Search Best Practices
**Search docs BEFORE making code changes** to ensure correct approach.

### Query Guidelines
- Use multiple, broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`
- DO NOT include package names - versions are auto-included: `test resource table` not `filament 4 test resource table`
- Pass multiple queries at once for best results

### Search Syntax
1. **Simple words** (auto-stemming): `authentication` finds 'authenticate' and 'auth'
2. **Multiple words** (AND logic): `rate limit` finds both "rate" AND "limit"
3. **Quoted phrases** (exact): `"infinite scroll"` finds words adjacent in that order
4. **Mixed queries**: `middleware "rate limit"` finds "middleware" AND exact phrase "rate limit"
5. **Multiple queries**: `["authentication", "middleware"]` finds ANY of these terms


=== php rules ===

## PHP Standards

### Type Safety
- Always use explicit return type declarations for methods and functions
- Always use type hints for method parameters
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) {}`
- No empty `__construct()` methods with zero parameters

<code-snippet name="Type Declarations Example" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

### Code Style
- Always use curly braces for control structures (even single-line)
- Prefer PHPDoc blocks over inline comments (only comment very complex logic)
- Add array shape type definitions in PHPDoc when appropriate
- Enum keys should be TitleCase: `FavoritePerson`, `BestLake`, `Monthly`


=== inertia-laravel rules ===

## Inertia.js (React + TypeScript)

This application uses **React** with Inertia.js v2 (NOT Vue.js).

### Core Concepts
- Use `Inertia::render()` for server-side routing (not Blade views)
- Page components are `.tsx` files in `resources/js/pages/` (lowercase folders)
- Use `search-docs` for accurate Inertia guidance

<code-snippet lang="php" name="Inertia::render Example">
Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::query()->get()
    ]);
});
</code-snippet>

### File Organization
- **Pages**: `resources/js/pages/` (e.g., `dashboard.tsx`, `user/create.tsx`)
- **Layouts**: `resources/js/layouts/` (e.g., `app-layout.tsx`)
- **Components**: `resources/js/components/` (e.g., `ui/button.tsx`)

### TypeScript Conventions
- Define props interface for each page component
- Use `<Head>` from `@inertiajs/react` for page titles
- Use `useForm` hook for form handling

### Inertia v2 Features
- **Polling** - Auto-refresh data at intervals
- **Prefetching** - Load data before navigation
- **Deferred props** - Load heavy data after page render (add skeleton/empty states)
- **Infinite scrolling** - Use merging props and `WhenVisible`
- **Lazy loading** - Load data on scroll


=== laravel/core rules ===

## Laravel Best Practices

### Artisan Commands
- Use `php artisan make:` for all file creation (migrations, controllers, models, etc.)
- Use `artisan make:class` for generic PHP classes
- Always pass `--no-interaction` and appropriate `--options`

### Database & Models
- **CRITICAL**: Always use `Model::query()` for all queries
  - ✅ `User::query()->where('email', $email)->first()`
  - ❌ `User::where('email', $email)->first()` or `User::all()`
- Prefer Eloquent relationships over raw queries or manual joins
- Use eager loading to prevent N+1 query problems
- Use proper return type hints for relationship methods

### Model Creation
**ALWAYS use `php artisan make:model {Name} -mfs --no-interaction`**
- `-m` creates migration, `-f` creates factory, `-s` creates seeder
- Example: `php artisan make:model Organization -mfs --no-interaction`

### Migrations
See `.ai/guidelines/database-migrations.blade.php` for details.
- **Remove `down()` method** - we don't roll back migrations
- **No default values** - defaults are business logic, not DB constraints
- **Column order**: `id()`, then `timestamps()`, then other columns
- **No `after()` method** - breaks PostgreSQL compatibility
- **No cascade constraints** - handle deletions in Actions
- Use `$table->foreignIdFor(Model::class)` for foreign keys

### Controllers & Validation
- Create Form Request classes (not inline validation)
- Include validation rules and custom error messages
- Check sibling Form Requests for array vs string-based rules convention

### Other Conventions
- **Configuration**: Use `config('app.name')` not `env('APP_NAME')` (env only in config files)
- **URLs**: Prefer named routes with `route()` function
- **APIs**: Use Eloquent API Resources and versioning (unless existing code differs)
- **Queues**: Use `ShouldQueue` interface for time-consuming operations
- **Auth**: Use built-in features (gates, policies, Sanctum)
- **Testing**: Use model factories, check for custom states, prefer feature tests over unit tests
- **Vite errors**: Suggest running `npm run build` or `composer run dev`


=== laravel/v12 rules ===

## Laravel 12 Streamlined Structure

### File Structure Changes
- **No `app/Http/Middleware/`** - register middleware in `bootstrap/app.php`
- **No `app/Console/Kernel.php`** - use `bootstrap/app.php` or `routes/console.php`
- **Commands auto-register** from `app/Console/Commands/`
- **`bootstrap/app.php`** - middleware, exceptions, routing
- **`bootstrap/providers.php`** - application service providers

### Database & Models
- When modifying columns, include ALL previous attributes (or they'll be dropped)
- Limit eager loaded records natively: `$query->latest()->limit(10);`
- Use `casts()` method instead of `$casts` property (follow existing conventions)

### Contextual Attributes (Dependency Injection)
**ALWAYS use contextual attributes** - cleaner, more explicit, type-safe.

**Available Attributes**:
- `#[CurrentUser]` - Currently authenticated user
- `#[Auth('guard')]` - Specific auth guard
- `#[Cache('store')]`, `#[Config('key')]`, `#[DB('connection')]`, `#[Storage('disk')]`
- `#[RouteParameter('name')]` - Route parameter

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

// ❌ WRONG - Don't inject Request just to get user
public function store(
    CreateInvitationData $data,
    CreateInvitation $action,
    Request $request
): RedirectResponse {
    $user = $request->user();
    // ...
}
```


=== laravel-data/core rules ===

## Spatie Laravel Data (DTOs)

**Type-safe Data Transfer Objects** - NOT for validation.

### Separation of Concerns
- **Form Requests** - HTTP validation (required fields, formats, rules)
- **Data Objects** - Type-safe data transfer between layers
- **Actions** - Business logic, receive type-safe Data objects

### Data Object Rules
- Store in `app/Data/` namespace
- **MUST suffix with `Data`**: `UpdateOfficeData`, `CreateOfficeData`, `AddressData`
  - ❌ NOT: `UpdateOfficeRequest`, `UpdateOffice`, `OfficeDto`
- **Use `Optional` for updates** (partial updates)
- **Use required types for creates** (all fields required)
- **Use `readonly` properties** (immutable)
- **NO validation attributes** (validation in Form Requests)

@boostsnippet('Update Data Object with Optional')
```php
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly OfficeType|Optional $type,
        public readonly string|Optional|null $phone,
        public readonly AddressData|Optional $address,
    ) {}
}
```

@boostsnippet('Create Data from Form Request')
```php
public function update(
    UpdateOfficeRequest $request,
    Office $office,
    UpdateOfficeAction $action
): RedirectResponse {
    $data = UpdateOfficeData::from($request->validated());
    $action->handle($data, $office);
    return redirect()->route('admin.settings.organization.edit');
}
```


=== pint/core rules ===

## Laravel Pint

Run `vendor/bin/pint --dirty` before finalizing changes (NOT `--test`, just run to fix).


=== pest/core rules ===

## Pest Testing

### Core Rules
- All tests use Pest: `php artisan make:test --pest <name>`
- **Never remove tests** without approval - they're core to the application
- Test happy paths, failure paths, and edge cases
- Tests live in `tests/Feature`, `tests/Unit`, `tests/Browser`

### Test Structure
- **Flat structure** - NO nested subdirectories (exception: organizing by type like `tests/Unit/Models/`)
  - ✅ `tests/Browser/AdminLayoutTest.php`
  - ❌ `tests/Browser/Admin/AdminLayoutTest.php`
- **New tests come first** in test files
- **Import all classes** - never use fully qualified names inline

### Type Safety in Tests
- **Type hint everything**: `test('example', function (): void { ... });`
- **PHPDoc for variables**:
  - Factories: `/** @var User $user */ $user = User::factory()->createQuietly();`
  - DB queries: `/** @var Role $role */ $role = Role::query()->where('name', 'employee')->first()?->fresh();`
  - Collections: `/** @var Collection<int, Permission> $permissions */`

### Critical Patterns
- **Use `createQuietly()`** not `create()` (prevents model events)
- **Use `fresh()`** when retrieving seeded/migration records
- **Chain expect() methods**: `expect($role)->not->toBeNull()->name->toBe('employee');`
- **Use `->throws()`** for exceptions (NOT `expect()->toThrow()`)

<code-snippet name="Pest Test Example" lang="php">
test('example', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Role $role */
    $role = Role::query()->where('name', 'employee')->first()?->fresh();

    expect($user->id)->toBeInt()
        ->and($role)->not->toBeNull();
});
</code-snippet>

<code-snippet name="Exception Testing" lang="php">
test('validates required email', function () {
    CreateInvitationData::validateAndCreate([]);
})->throws(ValidationException::class, 'email');
</code-snippet>

### Running Tests
- Run minimal tests with filters before finalizing: `php artisan test --filter=testName`
- Use specific methods for status codes: `assertForbidden()` not `assertStatus(403)`
- Use datasets for validation rule tests (reduce duplication)
- Import mocks: `use function Pest\Laravel\mock;` or use `$this->mock()`


=== pest/v4 rules ===

## Pest 4 Browser Testing

Pest v4 adds: browser testing, smoke testing, visual regression, test sharding, faster type coverage.

### Browser Tests (`tests/Browser/`)
- Use Laravel features: `Event::fake()`, `assertAuthenticated()`, model factories
- Interact with page: click, type, scroll, select, submit, drag-and-drop
- Test multiple browsers (Chrome, Firefox, Safari) or devices if needed
- Switch color schemes (light/dark mode) when appropriate
- Take screenshots for debugging

<code-snippet name="Browser Test Example" lang="php">
it('may reset password', function () {
    Notification::fake();
    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in');
    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors()
        ->click('Forgot Password?')
        ->fill('email', 'user@example.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!');

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);
$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>

### Configuration Best Practices
- **`RefreshDatabase` is global** - DO NOT add in individual test files
- **Use `$this->actingAs($user)`** - NOT `actingAs($user)` or `Auth::login($user)`
- **Use `visit()`** (no `$this->`) - it's globally available

<code-snippet name="Correct Pattern" lang="php">
it('admin can access users', function (): void {
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($admin);  // ✅
    $page = visit('/admin/users');  // ✅
    $page->assertSee('Users');
});
</code-snippet>


=== tailwindcss rules ===

## Tailwind CSS v4

### Core Principles
- Check and use existing Tailwind conventions before writing your own
- Extract repeated patterns into components (JSX/React)
- Think through class placement, order, priority - remove redundancies
- Use `gap` utilities for spacing (not margins)
- Match dark mode support: use `dark:` prefix if existing components support it

<code-snippet name="Spacing Example" lang="html">
<div class="flex gap-8">
    <div>Superior</div>
    <div>Michigan</div>
    <div>Erie</div>
</div>
</code-snippet>

### Tailwind v4 Changes
- Import via `@import "tailwindcss";` (NOT `@tailwind` directives)
- `corePlugins` not supported in v4

### Replaced Utilities
| Deprecated | Replacement |
|------------|-------------|
| bg-opacity-*, text-opacity-*, border-opacity-* | bg-black/*, text-black/*, border-black/* |
| flex-shrink-*, flex-grow-* | shrink-*, grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice, decoration-clone | box-decoration-slice, box-decoration-clone |


=== shadcn/ui rules ===

## shadcn/ui Components

**Re-usable components** built with Radix UI and Tailwind CSS.

### Component Usage
- **ALWAYS check `resources/js/components/ui/` FIRST** before creating new components
- Components are copied into codebase (not npm packages)
- Import from `@/components/ui/` path alias
- Use composition to build complex UI
- Use built-in variants instead of custom styling
- Built-in accessibility and dark mode support

### Available Components
button, card, input, label, select, dialog, sheet, dropdown-menu, separator, badge, avatar, skeleton

<code-snippet name="shadcn Example" lang="tsx">
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function MyForm() {
    return (
        <Card>
            <CardHeader>
                <CardTitle>User Information</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div>
                    <Label htmlFor="email">Email</Label>
                    <Input id="email" type="email" />
                </div>
                <Button type="submit">Save</Button>
            </CardContent>
        </Card>
    );
}
</code-snippet>

### Adding New Components
1. Check `resources/js/components/ui/` first
2. Check shadcn/ui documentation
3. Copy to `resources/js/components/ui/`
4. Ensure Tailwind v4 compatibility and dark mode support


=== react rules ===

## React 18 & TypeScript

### Component Structure
- **Functional components only** (no class components)
- **File naming**: lowercase with hyphens (`user-profile.tsx` not `UserProfile.tsx`)
- **Page components**: `export default`
- **Reusable components**: can use named exports
- **TypeScript**: Define props interface for every component

### TypeScript Conventions
- Type all props, state, and function parameters
- Prefer `interface` over `type` for props
- No `any` type - use proper types
- Use `import type` for type-only imports

<code-snippet name="React Component Example" lang="tsx">
import { type ReactNode } from "react";
import { Head } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";

interface DashboardProps {
    user: { name: string; email: string };
    stats: { totalUsers: number; activeUsers: number };
}

export default function Dashboard({ user, stats }: DashboardProps) {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="space-y-4">
                <h1>Welcome, {user.name}</h1>
                <p>Total Users: {stats.totalUsers}</p>
            </div>
        </AppLayout>
    );
}
</code-snippet>

### State & Forms
- **`useState`** - local component state
- **Inertia props** - server state (from Laravel)
- **`useForm`** - form state (from `@inertiajs/react`)
- **No Redux/Zustand** unless explicitly approved

### Inertia Forms
- Type-safe: define form data interface
- Validation: backend via Laravel Data objects
- Errors: `form.errors`, Loading: `form.processing`

<code-snippet name="Inertia Form Example" lang="tsx">
import { useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

interface FormData {
    name: string;
    email: string;
}

export default function UserForm() {
    const form = useForm<FormData>({ name: "", email: "" });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post("/users");
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div>
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    value={form.data.name}
                    onChange={(e) => form.setData("name", e.target.value)}
                />
                {form.errors.name && (
                    <p className="text-sm text-destructive">{form.errors.name}</p>
                )}
            </div>
            <Button type="submit" disabled={form.processing}>
                {form.processing ? "Saving..." : "Save"}
            </Button>
        </form>
    );
}
</code-snippet>



=== peopledear architecture ===

## PeopleDear Architecture

See `.ai/guidelines/app.actions.blade.php` for comprehensive Action pattern guidelines.

### Controllers (`app/Http/Controllers/`)
- **Flat hierarchy** - NO nested `Admin/` folders
- **Single-action controllers**: Use `__invoke()` (e.g., `ActivateUserController`)
- **Multi-action controllers**: Named methods for related actions (e.g., `UserController` with `index()`)
- Receive Actions/Queries via dependency injection
- Use Laravel 12 contextual attributes (`#[CurrentUser]`)

### Actions vs Queries
**Actions** (`app/Actions/`) - Create & update operations
- Create: `php artisan make:action "{name}" --no-interaction`
- Must implement `handle()` method (NOT `__invoke()`)
- Contain ALL business logic - keep models lean
- Wrap complex operations in `DB::transaction()`

**Queries** (`app/Queries/`) - Read operations
- Must implement `builder()` method returning Eloquent/Query Builder
- Descriptive names WITHOUT "Get" prefix (e.g., `UsersQuery` not `GetUsersQuery`)

### Lean Models Philosophy
**Models contain ONLY**:
- Relationships, simple accessors/mutators, casts, simple query scopes
- Simple boolean helpers (`isAdmin()`, `isPending()`)
- Simple defaults via `$attributes` property

**❌ NO update methods in Models** - all updates in Action classes

### Frontend Organization
- **Pages**: `resources/js/pages/` (lowercase, flat with optional grouping: `pages/admin/`)
- **Layouts**: `resources/js/layouts/`
- **Components**: `resources/js/components/`
- **UI primitives**: `resources/js/components/ui/` (shadcn/ui)

</laravel-boost-guidelines>

## Active Technologies
- PHP 8.4 with `declare(strict_types=1)` + Laravel 12, Spatie Laravel Data v4, Inertia.js v2 (002-timeoff-type-processors)
- PostgreSQL (existing VacationBalance table) (002-timeoff-type-processors)
- PHP 8.4 with strict typing, TypeScript 5 for frontend + Laravel 12, Inertia.js v2, React 18, shadcn/ui, Spatie Laravel Data (004-add-laravel-database-notifications)
- PostgreSQL (using Laravel's notifications table) (004-add-laravel-database-notifications)
- PostgreSQL (existing notifications table) (005-refactor-notifications)

## Recent Changes
- 002-timeoff-type-processors: Added PHP 8.4 with `declare(strict_types=1)` + Laravel 12, Spatie Laravel Data v4, Inertia.js v2
