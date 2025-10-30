# Data Objects Guidelines

## Purpose

**Data Objects are DTOs (Data Transfer Objects)** - NOT validation layers. They provide type-safe data transfer between application layers.

## Key Principles

1. **Form Requests handle validation** - NOT Data objects
2. **Data objects are created from validated data** - Use `::from($request->validated())`
3. **All properties should be optional (nullable)** - To handle partial updates
4. **No validation attributes** - Validation is done in Form Requests

## Using Data Objects with Inertia (Frontend)

### CamelCase for Frontend Properties

**When passing data to Inertia frontend, use Data objects with `CamelCaseMapper`:**

✅ **CORRECT - Use CamelCaseMapper for frontend data:**
@boostsnippet('CountryData with CamelCase')
```php
<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Country;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(CamelCaseMapper::class)]
final class CountryData extends Data
{
    #[Computed]
    public string $displayName;

    /**
     * @param  array<string, string>  $name
     */
    public function __construct(
        public readonly int $id,
        public readonly string $isoCode,  // ✅ CamelCase property
        public readonly array $name,
    ) {
        $this->displayName = $this->resolveDisplayName();
    }

    private function resolveDisplayName(): string
    {
        if (isset($this->name['en'])) {
            return $this->name['en'];
        }

        if (isset($this->name['EN'])) {
            return $this->name['EN'];
        }

        $firstKey = array_key_first($this->name);
        if ($firstKey !== null) {
            return $this->name[$firstKey];
        }

        return $this->isoCode;
    }
}
```

**In Controller:**
@boostsnippet('Use Data Object with Inertia')
```php
public function create(CountryQuery $countryQuery): Response
{
    $countries = $countryQuery->builder()
        ->orderBy('iso_code')
        ->get()
        ->map(fn (Country $country) => CountryData::from($country));  // ✅ Use ::from() with model

    return Inertia::render('org/create', [
        'countries' => $countries,  // ✅ Automatically converted to CamelCase
    ]);
}
```

**Frontend receives:**
- `id` → `id` (unchanged)
- `isoCode` → `isoCode` (already CamelCase)
- `name` → `name` (unchanged)
- `displayName` → `displayName` (computed property)

**Benefits:**
- Consistent CamelCase naming for frontend (follows JavaScript/TypeScript conventions)
- Type-safe data structure
- Automatic transformation from Eloquent models
- Computed properties for derived data

### When to Use CamelCaseMapper

- ✅ **Use for frontend data** - When passing data to Inertia/React components
- ✅ **Use for API responses** - When building JSON APIs for frontend consumption
- ❌ **Don't use for internal data** - When passing data between Actions/Queries use SnakeCaseMapper or no mapper

### Computed Properties

Use `#[Computed]` attribute for properties that are derived from other properties. Computed properties are declared as properties (not methods) and initialized in the constructor:

@boostsnippet('Computed Property Example')
```php
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

final class CountryData extends Data
{
    #[Computed]
    public string $displayName;

    public function __construct(
        public readonly int $id,
        public readonly string $isoCode,
        public readonly array $name,
    ) {
        $this->displayName = $this->name['en'] ?? $this->name['EN'] ?? $this->name[array_key_first($this->name)] ?? $this->isoCode;
    }
}
```

**Key Points:**
- Computed properties are **properties** (not methods) marked with `#[Computed]` attribute
- They are **initialized in the constructor** using `$this->propertyName = ...`
- They are automatically included in `toArray()` output
- Property name is used directly (e.g., `displayName` not `displayName()`)
- Can access other properties using `$this->propertyName` in the constructor
- Useful for derived values, formatted strings, or conditional logic

**Example Usage:**
```php
// In Data object
#[Computed]
public string $fullName;

public function __construct(
    public string $firstName,
    public string $lastName,
) {
    $this->fullName = "{$this->firstName} {$this->lastName}";
}

// When serialized to array
$data->toArray(); // ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe']

// Access as property (not method)
$data->fullName; // 'John Doe' ✅
$data->fullName(); // Error ❌
```

**Reference:** [Spatie Laravel Data - Computed Properties](https://spatie.be/docs/laravel-data/v4/as-a-data-transfer-object/computed)

## Creating Data Objects

### Data Object Structure Requirements

**ALWAYS add `@method array<string, mixed> toArray()` annotation to all Data objects:**

@boostsnippet('Data Object with toArray Annotation')
```php
<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Office;

use App\Enums\PeopleDear\OfficeType;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(SnakeCaseMapper::class)]
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?OfficeType $type,
        public readonly ?string $phone,
    ) {}
}
```

**Why this annotation?**
- Provides IDE autocomplete for `toArray()` method
- Documents the return type explicitly
- Helps static analysis tools (PHPStan/Larastan)
- Required for all Data objects in this project

### From Form Requests (Controllers)
@boostsnippet('Create Data from Form Request')
```php
public function update(UpdateOfficeRequest $request): RedirectResponse
{
// Request is already validated by UpdateOfficeRequest
$data = UpdateOfficeData::from($request->validated());

$office = $this->updateOffice->handle($data, $office);

return redirect()->route('admin.settings.organization.edit');
}
```

### Basic Data Object Structure
@boostsnippet('Data Object Example')
```php
<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Enums\PeopleDear\OfficeType;
use App\Enums\PeopleDear\OfficeType;
use App\Enums\PeopleDear\OfficeType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Optional;

final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly ?string      $name,
        public readonly ?OfficeType  $type,
        public readonly ?string      $phone,
        public readonly ?AddressData $address,
    )
    {
    }
}

```

## Why No Validation in Data Objects?

**Separation of Concerns:**
- **Form Requests** - Handle HTTP-specific validation (required fields, formats, unique constraints, etc.)
- **Data Objects** - Provide type safety and structure for internal data flow
- **Actions** - Contain business logic, receive type-safe Data objects

**Benefits:**
- Single source of truth for validation rules (Form Requests)
- Data objects can be reused across different contexts (HTTP, Console, Jobs)
- Cleaner code - no duplicate validation logic
- Partial updates work correctly (null values don't erase existing data)

## Nested Data Objects

When you have related entities (like Office with Address), use nested Data objects:

@boostsnippet('Nested Data Objects')
```php
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly ?string      $name,
        public readonly ?OfficeType  $type,
        public readonly ?string      $phone,
        public readonly ?AddressData $address, // Nested Data object
    )
    {
    }
}

final class AddressData extends Data
{
    public function __construct(
        public readonly ?string $line1,
        public readonly ?string $line2,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $postal_code,
        public readonly ?string $country,
    )
    {
    }
}
```

## Property Types: Create vs Update

### Create Data Objects
**Use required types** - Fields must be provided:

@boostsnippet('Create Data Object')
```php

final class CreateOfficeData extends Data
{
    public function __construct(
        public readonly string      $name,          // ✅ Required
        public readonly OfficeType  $type,      // ✅ Required
        public readonly ?string     $phone,        // ✅ Nullable (can be null)
        public readonly AddressData $address,  // ✅ Required nested
    )
    {
    }
}
```

### Update Data Objects
**Use Optional type** - Fields can be absent from request (partial updates):

@boostsnippet('Update Data Object with Optional')
```php

final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly string|Optional      $name,           // Can be absent
        public readonly OfficeType|Optional  $type,       // Can be absent
        public readonly string|Optional|null $phone,     // Can be absent OR null
        public readonly AddressData|Optional $address,   // Can be absent
    )
    {
    }
}
```

### Optional vs Nullable

**`Optional`** - Field was not provided in the request (don't update it)
**`null`** - Field was provided but set to null (update to null)
**`string | Optional | null`** - Field can be absent, or can be explicitly set to null

@boostsnippet('Handling Optional in Actions - Use toArray()')
```php
public function handle(PeopleDear\Office\UpdateOfficeData $data, Office $office): Office
{
    // toArray() automatically excludes Optional fields!
    $office->update($data->toArray());

    return $office->refresh();
}
```

**How `toArray()` works with Optional:**
- Fields with `Optional` are excluded from the array
- Fields with `null` are included with null value
- Only provided fields are in the resulting array

### Why Optional Matters

**Without Optional (Wrong):**
```php
// Request: { "name": "New Name" }  (phone not sent)
// Data: UpdateOfficeData { name: "New Name", phone: null }
// Result: Phone gets set to null! ❌
```

**With Optional (Correct):**
```php
// Request: { "name": "New Name" }  (phone not sent)
// Data: UpdateOfficeData { name: "New Name", phone: Optional }
// Result: Phone stays unchanged! ✅
```

## Common Mistake to Avoid

❌ **DON'T validate in Data objects:**
```php
// WRONG - Don't do this
final class UpdateOfficeData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]  // ❌ Don't validate here
        public readonly string $name,
    )
    {
    }
}
```

✅ **DO validate in Form Requests:**
```php
// CORRECT - Validate in Form Request
final class UpdateOfficeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],  // ✅ Validate here
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

## Usage Flow

```
HTTP Request
    → Form Request(validates)
    → Controller(creates Data object from validated data)
    → Action(receives type - safe Data object)
    → Returns result
```

## Testing Data Objects

**ALWAYS create tests for Data objects** to verify they handle Optional correctly:

@boostsnippet('Data Object Tests')
```php

test('update organization data with all fields', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
        'vat_number' => 'VAT123',
        'ssn' => 'SSN123',
        'phone' => '+1234567890',
    ]);

    expect($data->name)->toBe('Test Company')
        ->and($data->vat_number)->toBe('VAT123')
        ->and($data->ssn)->toBe('SSN123')
        ->and($data->phone)->toBe('+1234567890');
});

test('update organization data with partial fields', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
    ]);

    expect($data->name)->toBe('Test Company')
        ->and($data->vat_number)->toBeInstanceOf(Optional::class)
        ->and($data->ssn)->toBeInstanceOf(Optional::class)
        ->and($data->phone)->toBeInstanceOf(Optional::class);
});

test('update organization data toArray excludes optional fields', function (): void {
    $data = UpdateOrganizationData::from([
        'name' => 'Test Company',
        'phone' => null,
    ]);

    $array = $data->toArray();

    expect($array)->toHaveKeys(['name', 'phone'])
        ->not->toHaveKey('vat_number')
        ->not->toHaveKey('ssn')
        ->and($array['name'])->toBe('Test Company')
        ->and($array['phone'])->toBeNull();
});
```

@boostsnippet('Complete Example')
```php
// 1. Form Request - Validation
final class UpdateOfficeRequest extends FormRequest
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
            'address.line1' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
        ];
    }
}

// 2. Data Object - Type safety
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly ?string      $name,
        public readonly ?OfficeType  $type,
        public readonly ?string      $phone,
        public readonly ?AddressData $address,
    )
    {
    }
}

// 3. Controller - Bridge
public function update(
    UpdateOfficeRequest $request,
    Office              $office,
    UpdateOfficeAction  $action
): RedirectResponse
{
    $data = PeopleDear\Office\UpdateOfficeData::from($request->validated());
    $action->handle($data, $office);
    return redirect()->route('admin.settings.organization.edit');
}

// 4. Action - Business logic
public function handle(PeopleDear\Office\UpdateOfficeData $data, Office $office): Office
{
    // toArray() automatically excludes Optional fields!
    $office->update($data->toArray());

    if (!($data->address instanceof Optional)) {
        $office->address->update($data->address->toArray());
    }

    return $office->refresh();
}
```
