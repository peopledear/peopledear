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
