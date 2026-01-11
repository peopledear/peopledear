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
* @property-read Collection
<int, Office> $offices
*/
```

### Relationship Method Return Types
**ALWAYS add PHPDoc return type hints with PHPStan generics** for all relationship methods:

@boostsnippet('BelongsTo Relationship')
```php
/** @return BelongsTo
<Organization, $this> */
public function organization(): BelongsTo
{
return $this->belongsTo(Organization::class);
}
```

@boostsnippet('HasMany Relationship')
```php
/** @return HasMany
<Office, $this> */
public function offices(): HasMany
{
return $this->hasMany(Office::class);
}
```

@boostsnippet('MorphTo Relationship')
```php
/** @return MorphTo
<Model, $this> */
public function addressable(): MorphTo
{
return $this->morphTo();
}
```

@boostsnippet('MorphOne Relationship')
```php
/** @return MorphOne
<Address, $this> */
public function address(): MorphOne
{
return $this->morphOne(Address::class, 'addressable');
}
```

@boostsnippet('BelongsToMany Relationship')
```php
/** @return BelongsToMany
<Role, $this> */
public function roles(): BelongsToMany
{
return $this->belongsToMany(Role::class);
}
```

### Complete Model Example

@boostsnippet('Complete Model with Type Hints')
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\OfficeType;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property int $organization_id
 * @property string $name
 * @property OfficeType $type
 * @property string|null $phone
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Address $address
 */
final class Office extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return MorphOne<Address, $this> */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'name' => 'string',
            'type' => OfficeType::class,
            'phone' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

```

## Model Structure

### Order of Elements
1. PHPDoc block with `@property - read` annotations
2. Class declaration
3. Traits (e.g., `HasFactory`, `SoftDeletes`)
4. Relationship methods
5. `casts()` method
6. Other methods (scopes, accessors, mutators)

### Relationships
- **ALWAYS use explicit return type hints** - Both native PHP return type AND PHPDoc with generics
- **ALWAYS use `@property - read`** for all relationships in the class PHPDoc
- Keep relationship methods simple - just return the relationship, no business logic
- Use proper PHPStan generic syntax: `@return RelationType < RelatedModel, $this > `

### Casts
- **ALWAYS use public `casts()` method** (not `$casts` property or protected method) for Laravel 11+
- Cast enum types using `EnumName::class`
- Always cast `id` and foreign keys to `integer`
- Always cast timestamps to `datetime`

```php
public function casts(): array
{
    return [
        'id' => 'integer',
        'type' => OfficeType::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

## Benefits
- **IDE Autocomplete**: `$office->organization->` shows Organization methods
- **Static Analysis**: PHPStan/Larastan can detect type errors
- **Better Refactoring**: IDEs can track relationships across codebase
- **Documentation**: Developers immediately see what relationships exist
