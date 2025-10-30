## Eloquent model standards

- **Type Hints Required**: Add PHPDoc `@property` and `@property-read` annotations for all database fields, casts, and relationships
- **Relationship Return Types**: Always add PHPDoc return type hints with PHPStan generics (e.g., `@return BelongsTo<Organization, $this>`)
- **Casts Method**: Use public `casts()` method (not `$casts` property) for Laravel 11+ with explicit type casting for id, foreign keys, enums, and timestamps
- **Readonly Relationships**: Use `@property-read` for ALL relationships since relationships are always read-only
- **Lean Models**: Keep models minimal - only relationships, simple accessors/mutators, casts, and simple boolean helpers (e.g., `isAdmin()`)
- **No Business Logic**: Do NOT add update/create methods to models - all business logic belongs in Action classes
- **Factory Type Hints**: Use `@use HasFactory<ModelFactory>` annotation for factory support
- **Final Classes**: Declare models as final to prevent inheritance and ensure immutability
- **Strict Types**: Always use `declare(strict_types=1);` at the top of model files
- **Clear Naming**: Use singular names for models and plural for table names
- **Model Structure Order**: PHPDoc block → class declaration → traits → relationship methods → casts() method → other methods
