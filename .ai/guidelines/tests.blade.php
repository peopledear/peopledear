# Testing Guidelines

## General Testing Principles

- **Full test coverage required** - Test happy paths, failure paths, edge cases
- **TDD approach** - Write tests first, then implementation
- **All tests must pass before committing**
- Use Pest for all tests (`php artisan make:test --pest`)

## Test Structure

### Flat Test Organization
**ALWAYS use flat structure** - NO nested subdirectories:
- ✅ Correct: `tests/Unit/Actions/CreateOfficeActionTest.php`
- ❌ Wrong: `tests/Unit/Actions/Office/CreateOfficeActionTest.php`
- Exception: Organizing by type is allowed (Models/, Actions/, Enums/)

### Test File Naming
- Test files end with `Test.php`
- Match the class being tested: `CreateOfficeAction` → `CreateOfficeActionTest`
- Place in appropriate directory: Unit/, Feature/, or Browser/

### Test Naming Convention

**ALWAYS use imperative mood for test names** - describe what the code does, not what "it" does:

@boostsnippet('Imperative Mood Test Names')
```php
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
```

**Why imperative mood?**
- More concise and readable
- Matches Pest/PHPUnit conventions
- Focuses on behavior, not the subject
- Cleaner test output

## Dependency Injection in Tests

### Actions - Use Container Resolution with beforeEach

**ALWAYS resolve Actions from container** - NEVER use `new`:

@boostsnippet('Action Test with beforeEach')
```php
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
```

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

@boostsnippet('Processor Test with beforeEach')
```php
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
```

### Reusable Test Data in beforeEach

**If models, actions, or other resources are reused across multiple tests, ALWAYS define them in `beforeEach`:**

@boostsnippet('Reusable Resources in beforeEach')
```php
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
```

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

@boostsnippet('Factory Usage in Tests')
```php
// Create models
/** @var User $user */
$user = User::factory()->createQuietly(['name' => 'Test']);

// Retrieve seeded records
/** @var Role $role */
$role = Role::query()
    ->where('name', 'employee')
    ->first()
    ?->fresh();
```

### Assertions
- **Chain expect() methods** for cleaner tests
- Use specific assertions: `assertOk()`, `assertForbidden()` not `assertStatus()`
- **Import all classes** - Never use fully qualified names inline

@boostsnippet('Chained Expect')
```php
expect($result->name)
    ->toBe('Test Name')
    ->and($result->email)
    ->toBe('test@example.com')
    ->and($result->active)
    ->toBeTrue();
```

## Exception Testing

**Use Pest's `->throws()` method:**

@boostsnippet('Exception Testing')
```php

test('validates required field', function () {
    $data = [];

    CreateUserData::validateAndCreate($data);
})->throws(ValidationException::class, 'email');
```

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
- `RefreshDatabase` applied globally in `tests / Pest . php`
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

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific file
php artisan test tests / Unit / Actions / CreateOfficeActionTest . php

# Run with filter
php artisan test--filter = "CreateOfficeActionTest"

# Stop on first failure
php artisan test--stop - on - failure
```

## Before Every Commit

**ALWAYS run tests before committing:**

```bash
php artisan test              # All tests must pass
vendor / bin / pint--dirty       # Format code
```
