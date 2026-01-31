# GitHub Copilot Commit Message Instructions

Follow the Conventional Commits specification.
The commit message should be structured as follows:

<type>(<scope>): <description>

[optional body]

[optional footer(s)]

## Rules

- The commit message must start with a type (e.g., feat, fix, docs, style, refactor, perf, test, build, ci)
- The type should be followed by an optional scope in parentheses, then a colon and a space.
- The first line (subject) should be a short, descriptive summary of 50 characters or less.
- The body should provide more detailed context, if necessary.
- Use the imperative mood (e.g., "Add feature" not "Added feature").

===

<laravel-boost-guidelines>
=== .ai/app.actions rules ===

# App/Actions guidelines

- This application uses the Action pattern and prefers for much logic to live in reusable and composable Action classes.
- Actions live in `app/Actions`, they are named based on what they do, with no suffix.
- Actions will be called from many different places: jobs, commands, HTTP requests, API requests, MCP requests, and more.
- Create dedicated Action classes for business logic with a single `handle()` method.
- Inject dependencies via constructor using private properties.
- Create new actions with `php artisan make:action "{name}" --no-interaction`
- Wrap complex operations in `DB::transaction()` within actions when multiple models are involved.
- Some actions won't require dependencies via `__construct` and they can use just the `handle()` method.

<code-snippet name="Example action class" lang="php">
<?php

declare(strict_types=1);

namespace App\Actions;

final readonly class CreateFavorite
{
    public function __construct(private FavoriteService $favorites)
    {
        //
    }

    public function handle(User $user, string $favorite): bool
    {
        return $this->favorites->add($user, $favorite);
    }
}
</code-snippet>

## Action Method Signatures

### Update Actions

**ALWAYS accept the model being updated** as a parameter:

<code-snippet name="Update Action Signature" lang="php">
<?php

// ✅ CORRECT - Accept the model
public function handle(UpdateOrganizationData $data, Organization $organization): Organization
{
    $organization->update($data->toArray());
    return $organization->refresh();
}

// ❌ WRONG - Query for the model inside
public function handle(UpdateOrganizationData $data): Organization
{
    $organization = Organization::query()->firstOrFail(); // ❌ Don't do this
    $organization->update($data->toArray());
    return $organization->refresh();
}
</code-snippet>

### Delete Actions

**ALWAYS accept the model being deleted** as a parameter:

<code-snippet name="Delete Action Signature" lang="php">
<?php

// ✅ CORRECT - Accept the model
public function handle(Office $office): void
{
    $office->delete();
}

// ❌ WRONG - Accept ID and query
public function handle(int $officeId): void
{
    $office = Office::query()->findOrFail($officeId); // ❌ Don't do this
    $office->delete();
}
</code-snippet>

### Create Actions

**Accept Data object and any required context** (user, parent models, etc.):

<code-snippet name="Create Action Signature" lang="php">
<?php

public function handle(CreateOfficeData $data, Organization $organization): Office
{
    $office = $organization->offices()->create($data->toArray());

    $office->address()->create($data->address->toArray());

    return $office->refresh();
}
</code-snippet>

## Using toArray() with Optional

**Data objects automatically handle Optional** - use `toArray()` for clean updates:

<code-snippet name="toArray with Optional" lang="php">
<?php

public function handle(UpdateOrganizationData $data, Organization $organization): Organization
{
    // toArray() excludes Optional fields automatically!
    // Only fields that were provided in the request are included
    $organization->update($data->toArray());

    return $organization->refresh();
}
</code-snippet>

## Action Naming Convention

**Action classes are named WITHOUT the "Action" suffix:**

- ✅ CORRECT: `CreateOrganization`, `UpdateOrganization`, `DeleteOffice`
- ❌ WRONG: `CreateOrganizationAction`, `UpdateOrganizationAction`, `DeleteOfficeAction`

**Action test files follow the same naming:**

- Action class: `app/Actions/CreateOrganization.php`
- Test file: `tests/Unit/Actions/CreateOrganizationTest.php`

This keeps action names clean and concise while maintaining clarity about their purpose.

## Testing Actions

**ALWAYS create unit tests for Actions** to verify business logic:

<code-snippet name="Action Tests" lang="php">
<?php

use App\Actions\Organization\UpdateOrganization;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Models\Organization;
use Spatie\LaravelData\Optional;

test('updates organization with all fields', function (): void {
    $action = app(UpdateOrganization::class);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Name',
        'phone' => 'Old Phone',
    ]);

    $data = UpdateOrganizationData::from([
        'name' => 'New Name',
        'vat_number' => 'VAT123',
        'ssn' => 'SSN123',
        'phone' => '+1234567890',
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('New Name')
        ->and($result->vat_number)->toBe('VAT123')
        ->and($result->ssn)->toBe('SSN123')
        ->and($result->phone)->toBe('+1234567890');
});

test('updates organization with partial fields only', function (): void {
    $action = app(UpdateOrganization::class);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Name',
        'phone' => '+9999999999',
        'vat_number' => 'OLD_VAT',
    ]);

    // Only update name - phone and vat_number should stay unchanged
    $data = UpdateOrganizationData::from([
        'name' => 'New Name',
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('New Name')
        ->and($result->phone)->toBe('+9999999999') // ✅ Unchanged
        ->and($result->vat_number)->toBe('OLD_VAT'); // ✅ Unchanged
});

test('can set fields to null explicitly', function (): void {
    $action = app(UpdateOrganization::class);

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Company',
        'phone' => '+1234567890',
    ]);

    // Explicitly set phone to null
    $data = UpdateOrganizationData::from([
        'phone' => null,
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('Test Company') // ✅ Unchanged
        ->and($result->phone)->toBeNull(); // ✅ Set to null
});
</code-snippet>

=== .ai/app.controllers rules ===

# Controller Guidelines

## Controller Responsibilities

Controllers are **thin HTTP adapters** that:
1. Validate requests (via Form Requests)
2. Convert validated data to Data objects
3. Call Actions to perform business logic
4. Return responses (Inertia renders, redirects, JSON)

Controllers should **NOT** contain business logic - that belongs in Actions.

## Structure

### Flat Hierarchy

- **Controllers live directly in `app/Http/Controllers/`** - NO nested folders
- Clear naming eliminates need for namespace nesting
- Examples: `UserController`, `LocationController`, `OrganizationController`

### Single vs Multi-Action Controllers

**Single Action Controllers** - Use `__invoke()` for one specific action:
```php
final readonly class ActivateUserController
{
    public function __invoke(User $user, ActivateUserAction $action): RedirectResponse
    {
        $action->handle($user);
        return redirect()->back();
    }
}
```

**Multi-Action Controllers** - Use named methods for related CRUD operations:
```php
final readonly class LocationController
{
    public function store(CreateLocationRequest $request): RedirectResponse { }
    public function update(UpdateLocationRequest $request, Location $office): RedirectResponse { }
    public function destroy(Location $office): RedirectResponse { }
}
```

## Request Validation

### Always Use Form Requests

**ALWAYS create dedicated Form Request classes** - NEVER use inline validation:

<code-snippet name="Form Request Example" lang="php">
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organizations.edit');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:1,2,3,4,5,6,7,8'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'array'],
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['required', 'string', 'max:255'],
            'address.country' => ['required', 'string', 'max:255'],
        ];
    }
}
</code-snippet>

### Create Form Requests

```bash
php artisan make:request UpdateLocationRequest --no-interaction
```

## Controller Flow Pattern

<code-snippet name="Complete Controller Example" lang="php">
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Location\CreateLocation;
use App\Actions\Location\DeleteLocation;
use App\Actions\Location\UpdateLocation;
use App\Data\PeopleDear\Location\CreateLocationData;
use App\Data\PeopleDear\Location\UpdateLocationData;
use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class LocationController
{
    public function store(
        CreateLocationRequest $request,
        CreateLocation        $action,
        #[CurrentUser] User $user
    ): RedirectResponse
    {
        $data = CreateLocationData::from($request->validated());

        $action->handle($data, $user);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Location created successfully');
    }

    public function update(
        UpdateLocationRequest $request,
        Location              $office,
        UpdateLocation        $action
    ): RedirectResponse
    {
        $data = UpdateLocationData::from($request->validated());

        $action->handle($data, $office);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Location updated successfully');
    }

    public function destroy(
        Location       $office,
        DeleteLocation $action
    ): RedirectResponse
    {
        $action->handle($office);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Location deleted successfully');
    }
}
</code-snippet>

## Dependency Injection

### Method-Level Injection

**ALWAYS inject Actions at the method level** - NOT in `__construct()`:

✅ **CORRECT - Method-level injection:**
```php
public function store(
    CreateLocationRequest $request,
    CreateLocation        $action,  // ✅ Injected here
    #[CurrentUser] User $user
): RedirectResponse
{
    $data = CreateLocationData::from($request->validated());
    $action->handle($data, $user);
    return redirect()->route('admin.settings.organization.edit');
}
```

❌ **WRONG - Constructor injection:**
```php
public function __construct(
    private CreateLocation $createLocation,  // ❌ Don't do this
)
{
}

public function store(CreateLocationRequest $request): RedirectResponse
{
    $this->createLocation->handle(...);  // ❌ Wrong pattern
}
```

### Use Laravel 12 Contextual Attributes

**Always use `#[CurrentUser]` instead of `Request::user()`:**

<code-snippet name="CurrentUser Attribute" lang="php">
public function store(
    CreateLocationRequest $request,
    #[CurrentUser] User $user  // ✅ Clean and explicit
): RedirectResponse
{
    $data = CreateLocationData::from($request->validated());
    $this->createLocation->handle($data, $user);
    return redirect()->route('admin.settings.organization.edit');
}
</code-snippet>

❌ **Don't inject Request just to get user:**
```php
public function store(
    CreateLocationRequest $request,
    Request             $httpRequest  // ❌ Unnecessary
): RedirectResponse
{
    $user = $httpRequest->user();  // ❌ Verbose
    // ...
}
```

## Return Types

- **Inertia Pages** - Return `Response` (from `Inertia::render()`)
- **Redirects** - Return `RedirectResponse`
- **JSON APIs** - Return `JsonResponse`
- **Always use explicit return type hints**

## What NOT to Put in Controllers

❌ **Business Logic** - Belongs in Actions
❌ **Database Queries** - Belongs in Actions/Queries
❌ **Validation Logic** - Belongs in Form Requests
❌ **Data Transformation** - Belongs in Actions/Data objects

✅ **What Controllers SHOULD Do:**
- Type-hint Form Requests
- Create Data objects from validated data
- Call Actions
- Return HTTP responses

## Example: Wrong vs Right

❌ **WRONG - Business logic in controller:**
```php
public function update(UpdateLocationRequest $request, Location $office): RedirectResponse
{
    // ❌ Business logic in controller
    $office->update([
        'name' => $request->validated('name'),
        'type' => $request->validated('type'),
    ]);

    // ❌ More business logic
    if ($request->has('address')) {
        $office->address->update($request->validated('address'));
    }

    return redirect()->back();
}
```

✅ **CORRECT - Delegate to Action:**
```php
public function update(
    UpdateLocationRequest $request,
    Location              $office,
    UpdateLocation        $action
): RedirectResponse
{
    // ✅ Create Data object from validated data
    $data = UpdateLocationData::from($request->validated());

    // ✅ Delegate business logic to Action
    $action->handle($data, $office);

    // ✅ Return response
    return redirect()
        ->route('admin.settings.organization.edit')
        ->with('success', 'Location updated');
}
```

=== .ai/app.models rules ===

# Eloquent Model Guidelines

## Type Hinting

### Property Annotations

**ALWAYS add PHPDoc annotations for ALL properties** - database fields, casts, and relationships:

#### Database Fields & Casts

Use `@property` for writable database fields and `@property-read` for read-only fields (like timestamps, auto-incremented IDs):

```php
/**
 * @property-read int $id
 * @property string $name
 * @property string|null $vat_number
 * @property string|null $ssn
 * @property string|null $phone
 * @property OfficeType $type
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
```

#### Relationships

Use `@property-read` for ALL relationships (relationships are always read-only):

```php
/**
 * @property-read Organization $organization
 * @property-read Address $address
 * @property-read Collection<int, Office> $offices
 */
```

### Relationship Method Return Types

**ALWAYS add PHPDoc return type hints with PHPStan generics** for all relationship methods:

<code-snippet name="BelongsTo Relationship" lang="php">
/** @return BelongsTo<Organization, $this> */
public function organization(): BelongsTo
{
    return $this->belongsTo(Organization::class);
}
</code-snippet>

<code-snippet name="HasMany Relationship" lang="php">
/** @return HasMany<Office, $this> */
public function offices(): HasMany
{
    return $this->hasMany(Office::class);
}
</code-snippet>

<code-snippet name="MorphTo Relationship" lang="php">
/** @return MorphTo<Model, $this> */
public function addressable(): MorphTo
{
    return $this->morphTo();
}
</code-snippet>

<code-snippet name="MorphOne Relationship" lang="php">
/** @return MorphOne<Address, $this> */
public function address(): MorphOne
{
    return $this->morphOne(Address::class, 'addressable');
}
</code-snippet>

<code-snippet name="BelongsToMany Relationship" lang="php">
/** @return BelongsToMany<Role, $this> */
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}
</code-snippet>

=== .ai/app.queries rules ===

# Query Guidelines

## Query Responsibilities

Queries are **thin data access layers** that:
1. Encapsulate database read operations
2. Provide reusable query builders for complex queries
3. Keep controllers clean by moving query logic out of HTTP layer

Queries should **NOT** contain business logic - that belongs in Actions.

## Structure

### Location

- **Queries live in `app/Queries/`** directory
- Use descriptive names without "Get" prefix (e.g., `UsersQuery` not `GetUsersQuery`)
- One Query class per model/resource

### Naming Convention

- Use singular model name + "Query" suffix
- Examples: `CountryQuery`, `OrganizationQuery`, `UserQuery`

### Required Method

**ALL Queries MUST implement a `builder()` method** that returns an Eloquent or Query Builder instance:

<code-snippet name="Query Class Structure" lang="php">
<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

final class CountryQuery
{
    /**
     * @return Builder<Country>
     */
    public function builder(): Builder
    {
        return Country::query();
    }
}
</code-snippet>

## Usage in Controllers

### Dependency Injection

**ALWAYS inject Queries at the method level** - NOT in `__construct()`:

✅ **CORRECT - Method-level injection:**
```php
public function create(CountryQuery $countryQuery): Response
{
    $countries = $countryQuery->builder()
        ->orderBy('iso_code')
        ->get()
        ->map(fn (Country $country) => [
            'id' => $country->id,
            'iso_code' => $country->iso_code,
            'name' => $country->name['en'] ?? $country->name['EN'] ?? $country->name[array_key_first($country->name)] ?? $country->iso_code,
        ]);

    return Inertia::render('org/create', [
        'countries' => $countries,
    ]);
}
```

❌ **WRONG - Direct querying in controller:**
```php
public function create(): Response
{
    $countries = Country::query()  // ❌ Don't query directly in controller
        ->orderBy('iso_code')
        ->get();
    
    return Inertia::render('org/create', [
        'countries' => $countries,
    ]);
}
```

❌ **WRONG - Constructor injection:**
```php
public function __construct(
    private CountryQuery $countryQuery,  // ❌ Don't inject in constructor
)
{
}

public function create(): Response
{
    $countries = $this->countryQuery->builder()->get();  // ❌ Wrong pattern
}
```

## Query Building

### Basic Queries

Start with a simple `builder()` method that returns the base query:

```php
public function builder(): Builder
{
    return Country::query();
}
```

### Chaining in Controllers

Call `builder()` and chain additional query methods in the controller:

```php
public function index(CountryQuery $countryQuery): Response
{
    $countries = $countryQuery->builder()
        ->orderBy('iso_code')
        ->where('active', true)
        ->get();
    
    return Inertia::render('countries/index', [
        'countries' => $countries,
    ]);
}
```

### Complex Query Logic

If query logic becomes complex or reusable, add helper methods to the Query class:

```php
final class CountryQuery
{
    /**
     * @return Builder<Country>
     */
    public function builder(): Builder
    {
        return Country::query();
    }

    /**
     * @return Builder<Country>
     */
    public function active(): Builder
    {
        return $this->builder()->where('active', true);
    }

    /**
     * @return Builder<Country>
     */
    public function orderedByIsoCode(): Builder
    {
        return $this->builder()->orderBy('iso_code');
    }
}
```

Then use in controller:
```php
public function index(CountryQuery $countryQuery): Response
{
    $countries = $countryQuery->active()
        ->orderedByIsoCode()
        ->get();
    
    return Inertia::render('countries/index', [
        'countries' => $countries,
    ]);
}
```

## Type Hints

### Return Types

- **Always use explicit return type hints** for the `builder()` method
- Use generic type hints: `Builder<Model>`
- Example: `Builder<Country>`, `Builder<Organization>`

### PHPDoc

- Include PHPDoc blocks with return type annotations
- Helps IDE autocomplete and static analysis

## Testing

### Query Tests

Create tests for Query classes in `tests/Unit/Queries/`:

<code-snippet name="Query Test Example" lang="php">
<?php

declare(strict_types=1);

use App\Queries\CountryQuery;
use App\Models\Country;

beforeEach(function (): void {
    $this->query = new CountryQuery;
});

test('returns country query builder', function (): void {
    $builder = $this->query->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

test('builder returns countries', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly();

    $result = $this->query->builder()->first();

    expect($result)
        ->not->toBeNull()
        ->id->toBe($country->id);
});
</code-snippet>

## What NOT to Put in Queries

❌ **Business Logic** - Belongs in Actions
❌ **Write Operations** - Belongs in Actions
❌ **Data Transformation** - Can be done in controllers or use Transformers
❌ **Validation** - Belongs in Form Requests

✅ **What Queries SHOULD Do:**
- Return query builders
- Provide reusable query methods
- Encapsulate complex WHERE clauses
- Handle eager loading relationships

## Example: Wrong vs Right

❌ **WRONG - Business logic in Query:**
```php
public function builder(): Builder
{
    return Country::query()
        ->where('active', true)
        ->get()  // ❌ Don't execute queries in builder()
        ->map(fn ($country) => [  // ❌ Don't transform data in Query
            'id' => $country->id,
            'name' => $country->name['en'],
        ]);
}
```

✅ **CORRECT - Return builder, transform in controller:**
```php
// Query class
public function builder(): Builder
{
    return Country::query();
}

// Controller
public function create(CountryQuery $countryQuery): Response
{
    $countries = $countryQuery->builder()
        ->orderBy('iso_code')
        ->get()
        ->map(fn (Country $country) => [
            'id' => $country->id,
            'iso_code' => $country->iso_code,
            'name' => $country->name['en'] ?? $country->name['EN'] ?? $country->name[array_key_first($country->name)] ?? $country->iso_code,
        ]);

    return Inertia::render('org/create', [
        'countries' => $countries,
    ]);
}
```

## Queries vs Actions

### Use Queries For:

- Reading data (SELECT operations)
- Complex WHERE clauses
- Reusable query scopes
- Getting lists/collections

### Use Actions For:

- Creating records (INSERT)
- Updating records (UPDATE)
- Deleting records (DELETE)
- Business logic operations

## Integration with Actions

Actions can also use Queries when they need to read data:

```php
final readonly class CreateOrganization
{
    public function __construct(
        private CountryQuery $countryQuery,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {
        // Use query to validate country exists
        $countryExists = $this->countryQuery->builder()
            ->where('id', $data->country_id)
            ->exists();

        if (! $countryExists) {
            throw new InvalidArgumentException('Country does not exist');
        }

        $organization = Organization::query()->create([
            'name' => $data->name,
            'country_id' => $data->country_id,
        ]);

        return $organization->refresh();
    }
}
```

=== .ai/database.migrations rules ===

# Database Migration Guidelines

- **Column Order for CREATE TABLE**: ALWAYS use this exact order: `id()` first, then `timestamps()`, then all other columns
- **Column Order for ALTER TABLE**: Do NOT use `after()` method - simply add columns without position specification (using `after()` breaks PostgreSQL compatibility)
- **NEVER implement the `down()` method** - this application does not roll back migrations, always remove it
- Use `php artisan migrate:fresh --seed` to reset the database
- **NO default values in migrations** - default values are business logic, NOT database constraints
- Implement defaults in Model's `$attributes` property, Model's `booted()` method, Action classes, or Data Objects
- **ALWAYS use `foreignIdFor(Model::class)`** for foreign key columns
- Use the second parameter to customize column name: `foreignIdFor(User::class, 'invited_by_id')->constrained('users')`
- **NEVER add cascade constraints** - no `->onDelete('cascade')` or `->onUpdate('cascade')`
- Handle deletions explicitly in the application layer using Actions (cascading can lead to unintended data loss)
- When modifying a column, MUST include ALL attributes previously defined, otherwise they will be dropped

<code-snippet name="Correct CREATE TABLE column order" lang="php">
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->string('name');
    $table->string('email')->unique();
    $table->boolean('is_active');
});
</code-snippet>

<code-snippet name="Correct ALTER TABLE migration without after()" lang="php">
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
    // ✅ CORRECT - no after() method for PostgreSQL compatibility
});
</code-snippet>

<code-snippet name="Correct migration structure without down()" lang="php">
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email');
            $table->foreignIdFor(User::class, 'invited_by_id')->constrained('users');
            $table->foreignIdFor(Role::class);
            $table->timestamp('accepted_at')->nullable();
        });
    }
};
</code-snippet>

<code-snippet name="CORRECT - Default in Model attributes" lang="php">
class User extends Model
{
    protected $attributes = [
        'is_active' => true, // ✅ Simple defaults belong here
    ];
}
</code-snippet>

<code-snippet name="CORRECT - Context-dependent default in Action" lang="php">
class CreateUser
{
    public function handle(string $email, string $name, ?int $roleId = null): User
    {
        return User::query()->create([
            'email' => $email,
            'name' => $name,
            'role_id' => $roleId ?? Role::query()->where('name', 'employee')->first()->id,
            // ✅ Business logic belongs in Actions
        ]);
    }
}
</code-snippet>

<code-snippet name="Correct foreign keys without cascade" lang="php">
Schema::create('invitations', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->foreignIdFor(User::class, 'invited_by_id')->constrained('users');
    // ✅ Explicit table name when column name differs
    $table->foreignIdFor(Role::class);
    // ✅ Auto-infers 'role_id' and 'roles' table
    // ❌ NO ->onDelete('cascade') or ->onUpdate('cascade')
});
</code-snippet>

<code-snippet name="Column modification preserving all attributes" lang="php">
Schema::table('users', function (Blueprint $table) {
    $table->string('email')->unique()->nullable()->change();
    // ✅ MUST include ALL previous attributes or they will be lost
});
</code-snippet>

=== .ai/general rules ===

# General Guidelines

- Don't include any superfluous PHP Annotations, except ones that start with `@` for typing variables.

=== .ai/tests rules ===

# Testing Guidelines

## General Testing Principles

- **Full test coverage required** - Test happy paths, failure paths, edge cases
- **All tests must pass before committing**
- use `composer test:lint` to check code style
- Use `composer test` to run all the test suite
- **ALWAYS use `test('description', function () { ... });` syntax** - NEVER use `it()`.

## Test Structure

### Flat Test Organization

**ALWAYS use flat structure** - NO nested subdirectories:
- ✅ Correct: `tests/Unit/Actions/CreateLocationTest.php`
- ❌ Wrong: `tests/Unit/Actions/Location/CreateLocationTest.php`
- Exception: Organizing by type is allowed (Models/, Actions/, Enums/)
- Exception: For multi-tenant applications, use Landlord/ and Tenant/ subdirectories under Feature/ and Browser/ to scope tests appropriately (e.g., tests/Feature/Tenant/UserProfileControllerTest.php).

### Test File Naming

- Test files end with `Test.php`
- Match the class being tested: `CreateLocation` → `CreateLocationTest`
- Place in appropriate directory: Unit/, Feature/, or Browser/

### Test Naming Convention

**ALWAYS use imperative mood for test names** - describe what the code does, not what "it" does:

<code-snippet name="Imperative Mood Test Names" lang="php">
// ✅ CORRECT - Imperative mood (commands)
test('creates user with valid data', function (): void { ... });
test('validates required email field', function (): void { ... });
test('transforms arrays to JSON strings', function (): void { ... });
test('handles null values correctly', function (): void { ... });
test('preserves user id when updating', function (): void { ... });

// ❌ WRONG - "It" statements
test('it creates user with valid data', function (): void { ... });
test('it validates required email field', function (): void { ... });
test('it transforms arrays to JSON strings', function (): void { ... });
test('it handles null values correctly', function (): void { ... });
</code-snippet>

**Why imperative mood?**
- More concise and readable
- Matches Pest/PHPUnit conventions
- Focuses on behavior, not the subject
- Cleaner test output

## Dependency Injection in Tests

### Actions - Use Container Resolution with beforeEach

**ALWAYS resolve Actions from container** - NEVER use `new`:

<code-snippet name="Action Test with beforeEach" lang="php">
<?php

use App\Actions\Organization\UpdateOrganization;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->action = app(UpdateOrganization::class);
});

test('updates organization with all fields', function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    $data = UpdateOrganizationData::from([
        'name' => 'New Name',
    ]);

    $result = $this->action->handle($organization, $data);

    expect($result->name)->toBe('New Name');
});
</code-snippet>

❌ **WRONG - Don't use new:**
```php
test('updates organization', function (): void {
    $action = new UpdateOrganization(); // ❌ Missing dependencies!
    // ...
});
```

✅ **CORRECT - Use app() in beforeEach:**
```php
beforeEach(function (): void {
    $this->action = app(UpdateOrganization::class); // ✅ Container resolves dependencies
});
```

### Why Container Resolution?

1. **Automatic Dependency Injection** - Container resolves constructor dependencies
2. **Consistency** - Same resolution as production code
3. **Testing Real Behavior** - Tests actual dependency wiring
4. **Refactoring Safety** - Adding dependencies doesn't break tests

### Processors, Validators & Other Classes

**Same pattern applies to ALL container-resolved classes** (Processors, Validators, Services, etc.):

<code-snippet name="Processor Test with beforeEach" lang="php">
<?php

use App\Processors\TimeOffType\VacationProcessor;
use App\Models\VacationBalance;

beforeEach(function (): void {
    /** @var Organization $organization */
    $this->organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $this->employee = Employee::factory()->createQuietly([
        'organization_id' => $this->organization->id,
    ]);

    /** @var VacationProcessor $processor */
    $this->processor = app(VacationProcessor::class);
});

test('deducts days from vacation balance when processed', function (): void {
    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->createQuietly([
        'employee_id' => $this->employee->id,
        // ...
    ]);

    $this->processor->process($request);  // ✅ Using $this->processor

    expect($balance->refresh()->taken)->toBe(800);
});
</code-snippet>

### Reusable Test Data in beforeEach

**If models, actions, or other resources are reused across multiple tests, ALWAYS define them in `beforeEach`:**

<code-snippet name="Reusable Resources in beforeEach" lang="php">
beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var Country $country */
        $this->country = Country::factory()->createQuietly();

        /** @var CreateOffice $action */
        $this->action = app(CreateOffice::class);
    });

test('creates office', function (): void {
    $data = CreateOfficeData::from([
        'country_id' => $this->country->id,  // ✅ Reusing from beforeEach
        'name' => 'HQ',
    ]);

    $office = $this->action->handle($data);  // ✅ Reusing from beforeEach

    expect($office->name)->toBe('HQ');
});
</code-snippet>

**When NOT to use beforeEach:**
- Data specific to a single test
- Variations that differ per test
- One-off test scenarios

## Test Conventions

### Type Hints & Exception Annotations

**ALWAYS type hint all variables and add @throws annotations before closures:**

```php
test('example',
    /**
     * @throws Throwable
     */
    function (): void {  // ✅ @throws annotation BEFORE closure
        /** @var User $user */  // ✅ Type hint
        $user = User::factory()->createQuietly();

        expect($user->id)->toBeInt();
    });
```

**For `beforeEach` hooks:**
```php
beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {  // ✅ @throws annotation BEFORE closure
        /** @var CreateOffice $action */
        $this->action = app(CreateOffice::class);
    });
```

**Why add @throws Throwable?**
- Tests can throw exceptions (database errors, validation failures, etc.)
- Makes exception handling explicit
- Helps static analysis tools understand test behavior
- Required for consistency across all test files
- **MUST be placed before the closure**, not before the test() or beforeEach() call

### Factory Methods

- **ALWAYS use `createQuietly()`** - Prevents events from firing
- **Use `fresh()`** for records from migrations/seeders
- **Pass data explicitly** - Don't rely on factory defaults in tests

<code-snippet name="Factory Usage in Tests" lang="php">
// Create models
/** @var User $user */
$user = User::factory()->createQuietly(['name' => 'Test']);

// Retrieve seeded records
/** @var Role $role */
$role = Role::query()
    ->where('name', 'employee')
    ->first()
    ?->fresh();
</code-snippet>

### Assertions

- **Chain expect() methods** for cleaner tests
- Use specific assertions: `assertOk()`, `assertForbidden()` not `assertStatus()`
- **Import all classes** - Never use fully qualified names inline

<code-snippet name="Chained Expect" lang="php">
expect($result->name)
    ->toBe('Test Name')
    ->and($result->email)
    ->toBe('test@example.com')
    ->and($result->active)
    ->toBeTrue();
</code-snippet>

## Exception Testing

**Use Pest's `->throws()` method:**

<code-snippet name="Exception Testing" lang="php">
test('validates required field', function () {
    $data = [];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');
</code-snippet>

❌ **WRONG - Don't wrap in expect():**
```php
expect(fn() => CreateUserData::validateAndCreate([]))
    ->toThrow(ValidationException::class); // ❌
```

## Authentication in Tests

**Use `$this->actingAs()`** for authentication:

```php
test('authorized user can access', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();
    $user->assignRole('owner');

    $this->actingAs($user);  // ✅ Correct

    $response = $this->get(route('admin.settings'));

    $response->assertOk();
});
```

## Browser Tests (Pest v4)

### Using visit()

**Use `visit()` function** (no `$this->`):

```php
test('page renders correctly', function (): void {
    /** @var User $user */
    $user = User::factory()->createQuietly();

    $this->actingAs($user);

    $page = visit('/dashboard');  // ✅ No $this->

    $page->assertSee('Welcome');
});
```

### Global Configuration

- `RefreshDatabase` applied globally in `tests/Pest.php`
- Don't add `uses(RefreshDatabase::class)` in individual tests

## Test Organization

### New Tests First

**Place newly written tests at the TOP of the file:**

```php
// ✅ New test here
test('new feature works', function (): void {
    // ...
});

// Existing tests below
test('existing feature works', function (): void {
    // ...
});
```

### Running Tests

- To run style checks: `composer test:lint`.
- To run all tests: `composer test`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
- At the end of each work session, run the full test suite to ensure everything passes.

### Before Every Commit

**ALWAYS run:**
- `composer test:lint` to check code style
- `composer test` to run the full test suite

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.17
- filament/filament (FILAMENT) - v5
- inertiajs/inertia-laravel (INERTIA) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- livewire/livewire (LIVEWIRE) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- @inertiajs/react (INERTIA) - v2
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `wayfinder-development` — Activates whenever referencing backend routes in frontend components. Use when importing from @/actions or @/routes, calling Laravel routes from TypeScript, or working with Wayfinder route functions.
- `pest-testing` — Tests applications using the Pest 4 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, browser testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `inertia-react-development` — Develops Inertia.js v2 React client-side applications. Activates when creating React pages, forms, or navigation; using &lt;Link&gt;, &lt;Form&gt;, useForm, or router; working with deferred props, prefetching, or polling; or when user mentions React with Inertia, React pages, React forms, or React navigation.
- `tailwindcss-development` — Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use strict typing at the head of a `.php` file: `declare(strict_types=1);`.
- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Enums

- That being said, keys in an Enum should follow existing application Enum conventions.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd and will be available at: `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs for the user.
- You must not run any commands to make the site available via HTTP(S). It is always available through Laravel Herd.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

=== inertia-laravel/v2 rules ===

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, prefetching.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `bun run build` or ask the user to run `bun run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== wayfinder/core rules ===

# Laravel Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Invokable Controllers: `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Parameter Binding: Detects route keys (`{post:slug}`) — `show({ slug: "my-post" })`.
- Query Merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL, `null` removes params.
- Inertia: Use `.form()` with `<Form>` component or `form.submit(store())` with useForm.

=== pint/core rules ===

# Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.
</laravel-boost-guidelines>
