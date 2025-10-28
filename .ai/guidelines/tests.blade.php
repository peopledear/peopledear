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

## Test Conventions

### Type Hints
**ALWAYS type hint all variables in tests:**

```php
test('example', function (): void {  // ✅ Void return type
    /** @var User $user */  // ✅ Type hint
    $user = User::factory()->createQuietly();

    expect($user->id)->toBeInt();
});
```

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
