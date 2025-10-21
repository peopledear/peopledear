<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.12
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- larastan/larastan (LARASTAN) - v3
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4
- spatie/laravel-data (LARAVEL-DATA) - v4


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.
- **Always chain methods on new lines** - each chained method call should be on its own line for better readability

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Git Workflow
- **Always create a new feature branch** when starting a new task or feature
- Before creating a new branch, fetch and pull the latest changes from main: `git fetch && git pull origin main`
- Create feature branches with descriptive names: `git checkout -b feature/descriptive-name`
- **ALWAYS run `php artisan test` before every commit** - all tests must pass
- **ALWAYS run `vendor/bin/pint --dirty` before every commit** - code must be formatted
- Commit and push changes to the feature branch
- Only merge to main after all tests pass and code is reviewed

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- **ALWAYS use `Model::query()` for querying models** - NEVER use `Model::all()`, `Model::find()`, `Model::where()` directly
  - Correct: `User::query()->where('email', $email)->first()`
  - Incorrect: `User::where('email', $email)->first()`
  - Correct: `User::query()->get()`
  - Incorrect: `User::all()`
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Migrations
- **Always remove the `down()` method from migrations** - we don't roll back migrations in this application
- **No default values in migrations** - default values are business logic, NOT database constraints. Implement them in:
  - Model's `$attributes` property for simple defaults (e.g., `'is_active' => true`)
  - Model's `booted()` method for complex defaults or logic
  - Action classes for context-dependent defaults
  - Required method arguments or Data Objects for enforcing required values
- **Use `php artisan migrate:fresh --seed`** - since we don't have down methods, always use migrate:fresh to reset the database
- **Column order in migrations**:
  - For CREATE TABLE migrations: `id` column first, then `timestamps()`, then all other columns
  - For ALTER TABLE migrations: Do NOT use `after()` method - it's MySQL-only and breaks PostgreSQL compatibility
  - Simply add new columns without position specification - they will be appended to the table
- Use `$table->foreignIdFor(Model::class)` for foreign keys

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

### Contextual Attributes for Dependency Injection
- **ALWAYS use Laravel 12's contextual attributes for common dependencies** - cleaner and more expressive than manual injection
- **Available Attributes**:
  - `#[CurrentUser]` - Inject the currently authenticated user
  - `#[Auth('guard')]` - Inject a specific authentication guard
  - `#[Cache('store')]` - Inject a specific cache store
  - `#[Config('key')]` - Inject a config value
  - `#[DB('connection')]` - Inject a specific database connection
  - `#[RouteParameter('name')]` - Inject a route parameter
  - `#[Storage('disk')]` - Inject a specific storage disk

**Use `#[CurrentUser]` instead of `Request::user()`**:

```php
// ✅ CORRECT - Use CurrentUser attribute
use App\Models\User;
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
use Illuminate\Http\Request;

public function store(
    CreateInvitationData $data,
    CreateInvitation $action,
    Request $request
): RedirectResponse {
    $user = $request->user();
    $invitation = $action->handle($data->email, $data->role_id, $user->id);
    return to_route('users.index');
}
```

**Benefits**:
- More explicit and readable
- Works everywhere dependency injection is supported (controllers, commands, jobs, middleware)
- Type-safe - no casting needed
- Cleaner method signatures


=== laravel-data/core rules ===

## Spatie Laravel Data

This application uses Spatie Laravel Data for type-safe request validation and data transfer objects.

### Data Objects
- **ALWAYS use Data objects for validation and data transfer** - DO NOT use FormRequest classes (except for auth-specific cases like LoginRequest)
- **All Data objects MUST be suffixed with `Data`**
  - Correct: `CreateInvitationData`, `UpdateUserProfileData`, `UpdateUserRoleData`
  - Incorrect: `CreateInvitationRequest`, `UpdateUserProfile`, `UpdateUserRoleDto`
- **Store Data objects in `app/Data/`** namespace
- **Create Data objects using**: `php artisan make:data NameData --namespace=Data`

### Data Object Structure
- Use readonly properties with type hints
- **Validation Strategy**:
  - **Default: Use validation attributes** from `Spatie\LaravelData\Attributes\Validation`
    - Better IDE support with autocomplete
    - Co-located with property definitions
    - Type-safe and checked by static analysis
  - **Use `rules()` method only for**:
    - Dynamic validation (e.g., `unique:users,email,{auth()->id()}`)
    - Complex Laravel Rule objects (e.g., `File::image()`, `Rule::dimensions()`)
    - Conditional validation based on runtime context
- Data objects automatically validate and cast data

### Example Data Object
```php
<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class CreateInvitationData extends Data
{
    public function __construct(
        #[Required, Email, Max(255)]
        public readonly string $email,
        #[Required]
        public readonly int $role_id,
    ) {
    }
}
```

### Using Data Objects in Controllers
- Type-hint Data objects in controller methods
- Laravel automatically validates and injects the Data object
- Access properties directly: `$data->email`, `$data->role_id`

```php
public function store(CreateInvitationData $data, CreateInvitation $action): RedirectResponse
{
    $invitation = $action->handle($data->email, $data->role_id, auth()->id());
    return redirect()->route('users.index');
}
```

### Creating Data Objects - Two Methods

Laravel Data provides two methods for creating Data objects with different validation behavior:

#### 1. `::validateAndCreate()` - WITH Validation (HTTP Requests, API calls)
- **ALWAYS use in HTTP controllers** - Laravel auto-injects with validation
- Runs all validation rules (attributes or rules() method)
- Throws `ValidationException` on validation failure
- Use when you need to ensure data integrity before processing

```php
// In controllers - Laravel auto-validates when type-hinted
public function store(CreateInvitationData $data): RedirectResponse
{
    // $data is already validated
    $invitation = $action->handle($data->email, $data->role_id);
}

// Manual validation when needed
$data = CreateInvitationData::validateAndCreate([
    'email' => $request->input('email'),
    'role_id' => $request->input('role_id'),
]);
```

#### 2. `::from()` - WITHOUT Validation (Console Commands, Jobs, Trusted Sources)
- **SKIPS all validation** - creates object directly from array
- Use in console commands, queued jobs, seeders, or trusted internal data
- Faster performance when validation is not needed
- Throws `CannotCreateData` only if required parameters are missing (not validation failure)

```php
// In console commands - skip validation for admin operations
$data = CreateInvitationData::from([
    'email' => $email,      // Can be invalid - no validation!
    'role_id' => $roleId,   // Can be non-existent - no validation!
]);

$invitation = $action->handle($data->email, $data->role_id);
```

**Key Difference**:
- `::validateAndCreate()` = Type casting + Validation rules
- `::from()` = Type casting only (no validation)

### Benefits
- Type-safe data handling with IDE autocompletion
- Automatic validation using attributes
- Automatic casting of data types
- Seamless Inertia.js integration
- Reduces boilerplate code
- Combines validation + DTOs in one class


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest

### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- **New tests should always come first in test files** - place newly written tests at the top of the file, before existing tests
- **ALWAYS import all classes used in tests** - never use fully qualified class names inline
  - Correct: `use Illuminate\Validation\ValidationException;` then use `ValidationException::class`
  - Incorrect: `\Illuminate\Validation\ValidationException::class` without import
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- **Full Test Coverage Required**: Write comprehensive tests for all features, covering happy paths, failure paths, edge cases, and error conditions.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- After implementing changes, run the full test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Testing Exceptions
- **ALWAYS use Pest's `->throws()` method for exception assertions** - DO NOT wrap in `expect()->toThrow()`
- Call the method directly and chain `->throws()` to assert the exception
- This is the clean, idiomatic Pest pattern

<code-snippet name="Correct Exception Testing Pattern" lang="php">
use Illuminate\Validation\ValidationException;

test('it validates required email', function () {
    $data = [];

    CreateInvitationData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');
</code-snippet>

<code-snippet name="WRONG Exception Testing Pattern - DO NOT USE" lang="php">
// ❌ WRONG - Do not use expect()->toThrow()
test('it validates required email', function () {
    $data = [];

    expect(fn () => CreateInvitationData::validateAndCreate($data))
        ->toThrow(ValidationException::class);
})->throws(ValidationException::class, 'email');
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== pest/v4 rules ===

## Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>

### Pest Configuration & Best Practices

- **Global Configuration in `tests/Pest.php`**:
  - `RefreshDatabase` trait is applied globally to all tests - DO NOT add `uses(RefreshDatabase::class)` in individual test files
  - Global `beforeEach` hook configures test environment (fake strings, prevent stray HTTP requests, freeze time)
  - Configuration applies to all test directories: Browser, Feature, and Unit

- **Test Methods**:
  - Use `$this->actingAs($user)` for authentication in tests - NOT `actingAs($user)` or `Auth::login($user)`
  - Use `visit()` function (no `$this->`) for Pest browser tests - it's globally available
  - Follow existing test patterns - check sibling test files for conventions

<code-snippet name="Correct Test Authentication Pattern" lang="php">
it('admin can access users page', function (): void {
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin);  // ✅ CORRECT

    $page = visit('/admin/users');  // ✅ CORRECT - no $this->

    $page->assertSee('Users');
});
</code-snippet>

<code-snippet name="WRONG Test Patterns - DO NOT USE" lang="php">
it('admin can access users page', function (): void {
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    actingAs($admin);  // ❌ WRONG - missing $this->
    Auth::login($admin);  // ❌ WRONG - use $this->actingAs() instead

    $page = $this->visit('/admin/users');  // ❌ WRONG - visit() not $this->visit()
});
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff"
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== tests rules ===

## Test Enforcement

- **Full Test Coverage Required**: Every change must be programmatically tested with comprehensive coverage.
- Write tests for all scenarios:
  - **Happy paths**: Normal, expected user flows
  - **Failure paths**: Invalid inputs, authorization failures, not found scenarios
  - **Edge cases**: Boundary conditions, null values, empty states
  - **Error conditions**: Database errors, external service failures
- Test both feature/integration tests AND unit tests where appropriate.
- All tests must pass before considering the implementation complete.
- Use `php artisan test` to run the full test suite regularly.


=== peopledear architecture ===

## PeopleDear Architecture Patterns

This application follows specific architecture patterns to maintain clean, testable, and maintainable code.

### Controller Structure
- **Flat Hierarchy**: Controllers live directly in `app/Http/Controllers/` - no nested `Admin/` folders
- **Clear Naming**: Controller names are descriptive enough without namespace nesting
- **Single Action Controllers**: Use `__invoke()` for controllers that handle one specific action
  - Examples: `ActivateUserController`, `DeactivateUserController`, `ResendInvitationController`
- **Multi-Action Controllers**: Use named methods for related actions
  - Examples: `UserController` with `index()`, `InvitationController` with `store()` and `destroy()`

### Request Validation
- **Type-Safe Methods**: Use Laravel's type-safe request methods instead of `validated()`
  - Use `$request->string('email')->toString()` instead of `$request->validated('email')`
  - Use `$request->integer('role_id')` instead of `$request->validated('role_id')`
  - Use `$request->boolean('is_active')` instead of `$request->validated('is_active')`
- **Form Requests**: Always create dedicated Form Request classes for validation rules

### Actions vs Queries
- **Actions** (`app/Actions/`): Handle create and update operations
  - Examples: `CreateInvitation`, `UpdateUserRole`, `ActivateUser`
  - Actions return the modified/created model
  - Actions can trigger side effects (sending emails, logging, etc.)
  - **Actions perform ALL business logic and updates** - keep models lean
  - **Actions must implement a `handle()` method** - NOT `__invoke()`
    - Correct: `public function handle(User $user): User`
    - Incorrect: `public function __invoke(User $user): User`
  - Controllers call Actions using the `handle()` method: `$action->handle($user)`
- **Queries** (`app/Queries/`): Handle read operations
  - Examples: `UsersQuery`, `PendingInvitationsQuery`, `AllRolesQuery`
  - Queries must implement a `builder()` method that returns an Eloquent or Query Builder instance
  - Controllers call `$query->builder()->paginate()` or `$query->builder()->get()`

### Lean Models Philosophy
- **Keep Models as lean as possible** - Models should contain ONLY:
  - Relationships (e.g., `hasMany()`, `belongsTo()`)
  - Simple attribute accessors/mutators
  - Casts
  - Simple query scopes
  - Simple boolean helper methods (e.g., `isAdmin()`, `isPending()`)
- **Do NOT add update methods to Models** (e.g., NO `activate()`, `deactivate()`, `accept()` methods)
  - All updates must be performed in Action classes
  - Actions own all business logic and state changes
  - Example: `ActivateUser` Action does `$user->update(['is_active' => true])`, not `$user->activate()`
- **Default Values**:
  - Use Model's `$attributes` property ONLY for simple defaults (e.g., `'is_active' => true`)
  - Enforce complex or context-dependent defaults in Action classes
  - Example: `CreateUser` Action explicitly assigns role based on business rules
- **Tests use Factories with explicit data**: In tests, always pass data explicitly through factories
  - Do NOT rely on Model `booted()` hooks or defaults for test data
  - Factories should explicitly create the data needed for each test scenario
  - Example: `User::factory()->create(['role_id' => $employeeRole->id])`
- This separation ensures:
  - Models stay simple and focused on data structure
  - Business logic is explicit and testable in Actions
  - Tests are clear about what data they're creating
  - No hidden magic in Model lifecycle hooks that makes code hard to understand

### Query Naming Convention
- Use descriptive names without "Get" prefix
- Examples: `UsersQuery` not `GetUsersQuery`, `PendingInvitationsQuery` not `GetPendingInvitationsQuery`

### Frontend Structure
- **Flat Page Structure**: Pages live directly in `resources/js/Pages/` with descriptive folder names
  - Use `resources/js/Pages/Users/Index.vue` not `resources/js/Pages/Admin/Users/Index.vue`
  - Use `resources/js/Pages/AcceptInvitation.vue` not `resources/js/Pages/Auth/AcceptInvitation.vue`
- **Nuxt UI 4**: Use Nuxt UI 4 components for all UI elements
  - `UButton`, `UModal`, `UTable`, `UInput`, `USelect`, `UDropdown`, `UBadge`, `UCard`, `UAlert`
- **Component Organization**: Create reusable components in `resources/js/Components/`

### Dependency Injection
- Controllers receive Actions and Queries via dependency injection
- Example:
```php
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
```

</laravel-boost-guidelines>