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

@boostsnippet('Query Class Structure')
```php
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
```

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

@boostsnippet('Query Test Example')
```php
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
```

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
