# ADR-0002: Separate Query Pattern for Data Retrieval

## Status

Accepted

## Context

Actions handle all write operations (create, update, delete) and business logic. However, Actions also need to read data from the database for validation and decision-making.

We considered two approaches:

1. **Mixed approach**: Actions perform both read and write operations directly
2. **Separated approach**: Actions only write, Query classes only read

### Issues with Mixed Approach

```php
// BAD: Action performs read and write
final readonly class MakeOrganizationIdentifier
{
    public function handle(string $name): string
    {
        $identifier = Str::slug($name);

        // Read operation mixed with business logic
        $exists = Organization::query()
            ->where('identifier', $identifier)
            ->exists();

        if ($exists) {
            $uniqueSuffix = Str::lower(Str::random(3));
            return $this->handle($name.' '.$uniqueSuffix);
        }

        return $identifier;
    }
}
```

Problems:

- Violates Single Responsibility Principle
- Read logic not reusable across Actions and Controllers
- Harder to test read logic independently
- Unclear what an Action should do

## Decision

Create **dedicated Query classes** for all database read operations. Actions delegate reading to Query classes.

### Pattern Rules

1. **Location**: All Queries live in `app/Queries/`
2. **Structure**:
    - Use `readonly` classes when using dependency injection
    - Use `declare(strict_types=1)`
    - **`__construct`**: Reserved for dependency injection only
    - **`__invoke`**: Initialize the Query builder and optionally apply initial filters, return `self`
    - Always provide a `builder()` method that returns the Builder
    - Return `self` from filter methods for fluent interface
3. **Usage**:
    - Actions inject Query classes via constructor
    - Controllers can also use Query classes directly
    - Invoke the Query to initialize: `(new RoleQuery())()` or `$query()`
4. **Consistency**:
    - Never use `make()` method - always use `builder()`
    - Always implement `__invoke` for initialization
    - Always return `clone $this` from `__invoke` to prevent builder state pollution
    - Reserve `__construct` for dependency injection only

### Example Structure

```php
declare(strict_types=1);

namespace App\Queries;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

final readonly class OrganizationQuery
{
    /**
     * @var Builder<Organization>
     */
    private Builder $builder;

    public function __invoke(): self
    {
        $this->builder = Organization::query();

        return clone $this;
    }

    /**
     * @return Builder<Organization>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }

    public function identifier(string $identifier): self
    {
        $this->builder->where('identifier', $identifier);
        return $this;
    }

    public function exists(): bool
    {
        return $this->builder->exists();
    }
}
```

### Usage in Action

```php
declare(strict_types=1);

namespace App\Actions\Organization;

use App\Queries\OrganizationQuery;
use Illuminate\Support\Str;

final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {
        // Invoke the Query to initialize, then use methods
        $exists = ($this->organizationQuery)()
            ->identifier(Str::slug($name))
            ->exists();

        if ($exists) {
            $uniqueSuffix = Str::lower(Str::random(3));
            return $this->handle($name.' '.$uniqueSuffix);
        }

        return Str::slug($name);
    }
}
```

### Invokable Pattern with Parameters

```php
declare(strict_types=1);

namespace App\Queries;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

final class RoleQuery
{
    /**
     * @var Builder<Role>
     */
    private Builder $builder;

    public function __invoke(UserRole|string|null $filter = null): self
    {
        $this->builder = Role::query();

        if ($filter !== null) {
            if ($filter instanceof UserRole) {
                $this->byRole($filter);
            } else {
                $this->byName($filter);
            }
        }

        return clone $this;
    }

    /**
     * @return Builder<Role>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }

    public function first(): ?Role
    {
        return $this->builder->first();
    }

    /**
     * @return Collection<int, Role>
     */
    public function get(): Collection
    {
        return $this->builder->get();
    }

    public function byRole(UserRole $role): self
    {
        $this->builder->where('name', $role->value);
        return $this;
    }

    public function byName(string $name): self
    {
        $this->builder->where('name', $name);
        return $this;
    }
}
```

### Dependency Injection Pattern

```php
declare(strict_types=1);

namespace App\Queries;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;

final readonly class CurrentEmployeeQuery
{
    /**
     * @var Builder<Employee>
     */
    private Builder $builder;

    public function __construct(
        #[CurrentUser] private ?User $user,
    ) {}

    public function __invoke(): self
    {
        $this->builder = Employee::query()
            ->where('user_id', $this->user?->id);

        // Always return clone to prevent builder state pollution
        return clone $this;
    }

    /**
     * @return Builder<Employee>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }
}
```

## Consequences

### Positive

- **Single Responsibility**: Actions write, Queries read
- **Reusability**: Queries can be used in Actions, Controllers, and other places
- **Testability**: Queries can be tested independently
- **Consistency**: Clear separation of concerns
- **Composability**: Queries can be chained and combined
- **Type Safety**: PHPStan generics provide proper type hints

### Negative

- **More Classes**: Increases number of classes
- **Indirection**: Adds a layer for read operations
- **Learning Curve**: Developers need to understand when to use Query vs Action

### Risks

- **Query Bloat**: Queries becoming too complex (mitigated by keeping them focused on filtering and retrieval)
- **Duplication**: Similar queries across different classes (mitigated by reusing Query classes)

### Important Implementation Notes

**Cloning in `__invoke`**: Always return `clone $this` from `__invoke()` to prevent builder state pollution. If you return `$this` directly, subsequent method calls on the same instance would continue modifying the same builder, leading to unexpected behavior when the query is reused in the same method.

## Related Decisions

- [ADR-0001: Use Action Pattern for Business Logic](0001-use-action-pattern.md)
- [ADR-0003: Domain Invariants in Actions](0003-domain-invariants-in-actions.md)

## References

- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html) - Related pattern
- [Query Object Pattern](https://www.martinfowler.com/eaaCatalog/queryObject.html) - Related pattern
- [Action Pattern documentation](../developer-guide/actions-pattern.md)
