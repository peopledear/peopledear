# Action Pattern

## Why Actions?

Actions are the cornerstone of our application's business logic layer. They provide:

- **Encapsulation**: All business logic for a specific operation lives in one place
- **Testability**: Actions are easily testable in isolation with Pest
- **Maintainability**: Single responsibility makes changes predictable
- **Composability**: Actions can inject and use other Actions
- **Transaction Safety**: Complex operations can be wrapped in database transactions

## What are Actions?

Actions are simple classes that encapsulate a single piece of business logic. They:

- Use a single `handle()` method as the entry point
- Are marked as `readonly` to ensure immutability
- Accept dependencies via constructor property promotion
- Return a model, `void`, or other appropriate types
- Live in `app/Actions/` organized by domain

## Action Responsibilities

### 1. Domain Invariant Rules

Actions must enforce all business rules that maintain data integrity. Domain invariants are conditions that must always be true for your system.

```php
// Example: MakeOrganizationIdentifier ensures unique organization identifiers
final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {
        $identifier = Str::slug($name);

        $exists = ($this->organizationQuery)()
            ->identifier($identifier)
            ->exists();

        if ($exists) {
            $uniqueSuffix = Str::lower(Str::random(3));
            return $this->handle($name.' '.$uniqueSuffix);
        }

        return $identifier;
    }
}
```

### 2. State Changes (Write Operations)

All database write operations (create, update, delete) must go through Actions.

```php
// Simple state change: CreateOrganization
final readonly class CreateOrganization
{
    public function __construct(
        private MakeOrganizationIdentifier $makeOrganizationSlug,
        private MakeOrganizationResourceKey $makeOrganizationResourceKey,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {
        $data->additional([
            'identifier' => $this->makeOrganizationSlug
                ->handle($data->name),
            'resource_key' => $this->makeOrganizationResourceKey
                ->handle(),
        ]);

        $organization = Organization::query()
            ->create($data->toArray());

        return $organization->refresh();
    }
}
```

### 3. Process Orchestration

Complex business processes that involve multiple operations are also Actions. They coordinate other Actions.

```php
// Complex process: CreatePeriod closes previous periods and creates new one
final readonly class CreatePeriod
{
    public function __construct(
        private ClosePeriods $closePeriods,
    ) {}

    public function handle(int $year, Organization $organization): void
    {
        DB::transaction(function () use ($year, $organization): void {
            $this->closePeriods->handle($year, $organization);

            $date = new CarbonImmutable($year);

            Period::query()->create([
                'organization_id' => $organization->id,
                'year' => $year,
                'start' => $date->startOfYear(),
                'end' => $date->endOfYear(),
                'status' => PeriodStatus::Active,
            ]);
        });
    }
}
```

## Query Separation

**Actions must NOT perform database queries directly for reading data.** All read operations use dedicated Query classes.

### Why Separate Queries?

- **Single Responsibility**: Actions write, Queries read
- **Reusability**: Queries can be reused across Actions and Controllers
- **Testability**: Easier to test queries in isolation
- **Performance**: Queries can be optimized independently

### Example: Action Using a Query

```php
// Action delegates reading to Query class
final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {
        $identifier = Str::slug($name);

        // Invoke the Query to initialize, then use methods
        $exists = ($this->organizationQuery)()
            ->identifier($identifier)
            ->exists();

        if ($exists) {
            $uniqueSuffix = Str::lower(Str::random(3));
            return $this->handle($name.' '.$uniqueSuffix);
        }

        return $identifier;
    }
}
```

### Query Class Structure

```php
// app/Queries/OrganizationQuery.php
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

## Action Types

### Simple Actions

Perform a single, straightforward operation.

```php
// ApproveTimeOffRequest - simple status update
final readonly class ApproveTimeOffRequest
{
    public function handle(TimeOffRequest $timeOffRequest): TimeOffRequest
    {
        $timeOffRequest->update([
            'status' => RequestStatus::Approved,
        ]);

        return $timeOffRequest->refresh();
    }
}
```

### Complex/Process Actions

Coordinate multiple operations, typically using database transactions.

```php
// CreateSystemRoles - creates roles, permissions, and assigns them
final class CreateSystemRoles
{
    public function __construct(
        private readonly PermissionRegistrar $permissionRegistrar,
        private readonly CreateRole $createRole,
        private readonly CreatePermission $createPermission,
        private readonly AssignPermissionToRole $assignPermissionToRole,
    ) {}

    public function handle(): void
    {
        $roles = UserRole::cases();

        $this->permissionRegistrar->forgetCachedPermissions();

        DB::transaction(function () use ($roles): void {
            foreach ($roles as $role) {
                $systemRole = $this->createRole->handle(roleName: $role);
                $permissions = $role->permissions();

                foreach ($permissions as $permission) {
                    if (! array_key_exists($permission->value, $this->permissionsCache)) {
                        $systemPermission = $this->createPermission->handle($permission);
                        $this->permissionsCache[$permission->value] = $systemPermission;
                    }

                    $this->assignPermissionToRole->handle(
                        $systemRole,
                        $this->permissionsCache[$permission->value]
                    );
                }
            }
        });
    }
}
```

## Action Composition

Actions can inject and use other Actions. This enables:

- **Code Reuse**: Common operations in dedicated Actions
- **Atomic Operations**: Multiple Actions wrapped in one transaction
- **Clear Dependencies**: Constructor injection makes relationships explicit

### Composition with Transactions

```php
// CreateTimeOffRequest - uses transaction even for single operation
final readonly class CreateTimeOffRequest
{
    public function handle(
        CreateTimeOffRequestData $data,
        Employee $employee,
        TimeOffType $timeOffType
    ): TimeOffRequest {
        return DB::transaction(function () use ($data, $timeOffType): TimeOffRequest {
            $timeOffRequest = TimeOffRequest::query()
                ->create([
                    ...$data->toArray(),
                    'status' => $timeOffType->requires_approval
                        ? RequestStatus::Pending
                        : RequestStatus::Approved,
                ]);

            return $timeOffRequest;
        });
    }
}
```

## Code Structure & Conventions

### Class Declaration

```php
// Always use strict types
declare(strict_types=1);

// Use readonly classes to prevent state mutation
final readonly class CreateOrganization
{
    // Use constructor property promotion
    public function __construct(
        private SomeAction $someAction,
    ) {}

    // Single handle method with explicit return type
    public function handle(SomeData $data): Model
    {
        // Implementation
    }
}
```

### Naming Conventions

- **No "Action" suffix**: `CreateOrganization` not `CreateOrganizationAction`
- **Verb-noun format**: Describes what it does
- **Past tense for completed state**: `Approved`, `Cancelled`
- **Present tense for process**: `Create`, `Update`, `Delete`

### Return Values

- **Create operations**: Return the created model
- **Update operations**: Return the updated model
- **Delete operations**: Return void (or boolean for success)
- **Process operations**: Return void

## Testing Actions

### Test Structure

```php
use App\Actions\Organization\CreateOrganization;
use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Country;
use App\Models\Organization;

beforeEach(function (): void {
    $this->action = resolve(CreateOrganization::class);
    $this->country = Country::factory()->create();
});

test('creates organization with provided data', function (): void {
    $data = new CreateOrganizationData(
        name: 'Test Organization',
        countryId: $this->country->id,
    );

    $organization = $this->action->handle($data);

    expect($organization)
        ->toBeInstanceOf(Organization::class)
        ->id->toBeString()
        ->name->toBe('Test Organization')
        ->country_id->toBe($this->country->id);
});
```

### Testing Transaction Rollback

```php
test('throws and exception when period already exists', function (): void {
    $organization = Organization::factory()->create();
    $year = 2023;

    $this->action->handle($year, $organization);

    $this->action->handle($year, $organization);

})->throws(UniqueConstraintViolationException::class);
```

## Real-World Examples

### Example 1: CreateOrganization (Simple + Injected Actions)

```php
// app/Actions/Organization/CreateOrganization.php
final readonly class CreateOrganization
{
    public function __construct(
        private MakeOrganizationIdentifier $makeOrganizationSlug,
        private MakeOrganizationResourceKey $makeOrganizationResourceKey,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {
        $data->additional([
            'identifier' => $this->makeOrganizationSlug->handle($data->name),
            'resource_key' => $this->makeOrganizationResourceKey->handle(),
        ]);

        $organization = Organization::query()
            ->create($data->toArray());

        return $organization->refresh();
    }
}
```

### Example 2: CreateSystemRoles (Complex + Transaction + Multiple Actions)

```php
// app/Actions/Role/CreateSystemRoles.php
final class CreateSystemRoles
{
    /**
     * @var array<string, Permission>
     */
    private array $permissionsCache = [];

    public function __construct(
        private readonly PermissionRegistrar $permissionRegistrar,
        private readonly CreateRole $createRole,
        private readonly CreatePermission $createPermission,
        private readonly AssignPermissionToRole $assignPermissionToRole,
    ) {}

    public function handle(): void
    {
        $roles = UserRole::cases();

        $this->permissionRegistrar->forgetCachedPermissions();

        DB::transaction(function () use ($roles): void {
            foreach ($roles as $role) {
                $systemRole = $this->createRole->handle(roleName: $role);
                $permissions = $role->permissions();

                foreach ($permissions as $permission) {
                    if (! array_key_exists($permission->value, $this->permissionsCache)) {
                        $systemPermission = $this->createPermission->handle($permission);
                        $this->permissionsCache[$permission->value] = $systemPermission;
                    }

                    $this->assignPermissionToRole->handle(
                        $systemRole,
                        $this->permissionsCache[$permission->value]
                    );
                }
            }
        });
    }
}
```

### Example 3: CreatePeriod (Complex + Injected Action + Transaction)

```php
// app/Actions/Period/CreatePeriod.php
final readonly class CreatePeriod
{
    public function __construct(
        private ClosePeriods $closePeriods,
    ) {}

    public function handle(int $year, Organization $organization): void
    {
        DB::transaction(function () use ($year, $organization): void {
            $this->closePeriods->handle($year, $organization);

            $date = new CarbonImmutable($year);

            Period::query()->create([
                'organization_id' => $organization->id,
                'year' => $year,
                'start' => $date->startOfYear(),
                'end' => $date->endOfYear(),
                'status' => PeriodStatus::Active,
            ]);
        });
    }
}
```

### Example 4: MakeOrganizationIdentifier (Uses Query Class)

```php
// app/Actions/Organization/MakeOrganizationIdentifier.php
final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {
        // Invoke the Query to initialize
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

### Example 5: CreateTimeOffRequest (Transaction for Single Operation)

```php
// app/Actions/TimeOffRequest/CreateTimeOffRequest.php
final readonly class CreateTimeOffRequest
{
    public function handle(
        CreateTimeOffRequestData $data,
        Employee $employee,
        TimeOffType $timeOffType
    ): TimeOffRequest {
        return DB::transaction(function () use ($data, $timeOffType): TimeOffRequest {
            $timeOffRequest = TimeOffRequest::query()
                ->create([
                    ...$data->toArray(),
                    'status' => $timeOffType->requires_approval
                        ? RequestStatus::Pending
                        : RequestStatus::Approved,
                ]);

            return $timeOffRequest;
        });
    }
}
```

## Anti-Patterns (What NOT to Do)

### ❌ Don't Put Queries in Actions

```php
// BAD: Direct database query in Action
final readonly class MakeOrganizationIdentifier
{
    public function handle(string $name): string
    {
        $identifier = Str::slug($name);

        // DON'T: Direct query in Action
        $exists = Organization::query()
            ->where('identifier', $identifier)
            ->exists();

        if ($exists) {
            return Str::slug($name.' '.Str::lower(Str::random(3)));
        }

        return $identifier;
    }
}
```

```php
// GOOD: Use Query class
final readonly class MakeOrganizationIdentifier
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
    ) {}

    public function handle(string $name): string
    {
        $identifier = Str::slug($name);

        // GOOD: Delegate to Query class (invoke to initialize)
        $exists = ($this->organizationQuery)()
            ->identifier($identifier)
            ->exists();

        if ($exists) {
            return $this->handle($name.' '.Str::lower(Str::random(3)));
        }

        return $identifier;
    }
}
```

### ❌ Don't Put Business Logic in Controllers

```php
// BAD: Business logic in Controller
class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validated();

        // DON'T: Business logic in Controller
        $organization = Organization::query()->create([
            'name' => $validated['name'],
            'identifier' => Str::slug($validated['name']),
            'resource_key' => Str::random(16),
        ]);

        return response()->json($organization);
    }
}
```

```php
// GOOD: Controller delegates to Action
class OrganizationController extends Controller
{
    public function store(CreateOrganizationRequest $request, CreateOrganization $action)
    {
        // GOOD: Controller delegates to Action
        $organization = $action->handle($request->toData());

        return response()->json($organization);
    }
}
```

### ❌ Don't Use "Action" Suffix

```php
// BAD: Has "Action" suffix
final readonly class CreateOrganizationAction
{
    public function handle(): void {}
}
```

```php
// GOOD: No "Action" suffix
final readonly class CreateOrganization
{
    public function handle(): void {}
}
```

### ❌ Don't Create Actions That Only Call Models

```php
// BAD: Just wraps model method
final readonly class UpdateUserName
{
    public function handle(User $user, string $name): User
    {
        $user->update(['name' => $name]);
        return $user->refresh();
    }
}
```

```php
// GOOD: Only create Actions when there's business logic
// Otherwise, let the Controller call the model directly (rare)
// or create an Action that does something meaningful
```

### ❌ Don't Skip Transactions for Multi-Step Operations

```php
// BAD: No transaction for multiple operations
final readonly class CreatePeriod
{
    public function handle(int $year, Organization $organization): void
    {
        // DON'T: Multiple operations without transaction
        $this->closePeriods->handle($year, $organization);

        Period::query()->create([...]);
    }
}
```

```php
// GOOD: Wrap in transaction
final readonly class CreatePeriod
{
    public function handle(int $year, Organization $organization): void
    {
        // GOOD: All operations in transaction
        DB::transaction(function () use ($year, $organization): void {
            $this->closePeriods->handle($year, $organization);

            Period::query()->create([...]);
        });
    }
}
```

### ❌ Don't Mix Read and Write Operations

```php
// BAD: Direct query in Action without using Query class
final readonly class ApproveTimeOffRequest
{
    public function handle(TimeOffRequest $timeOffRequest): TimeOffRequest
    {
        // DON'T: Direct model query in Action
        $balance = VacationBalance::query()
            ->where('employee_id', $timeOffRequest->employee_id)
            ->where('year', now()->year)
            ->sum('days');

        if ($balance < $timeOffRequest->days) {
            throw new InsufficientBalanceException();
        }

        $timeOffRequest->update(['status' => RequestStatus::Approved]);
        return $timeOffRequest->refresh();
    }
}
```

```php
// GOOD: Separate read and write - use Query class for validation
final readonly class ApproveTimeOffRequest
{
    public function __construct(
        private CurrentVacationBalanceQuery $balanceQuery,
    ) {}

    public function handle(TimeOffRequest $timeOffRequest): TimeOffRequest
    {
        // GOOD: Injected Query (invoke to initialize, then use builder)
        $balance = ($this->balanceQuery)()
            ->builder()
            ->sum('days');

        if ($balance < $timeOffRequest->days) {
            throw new InsufficientBalanceException();
        }

        $timeOffRequest->update(['status' => RequestStatus::Approved]);
        return $timeOffRequest->refresh();
    }
}
```

## Related Documentation

- [Architecture Decision: Use Action Pattern](../architecture-decisions/0001-use-action-pattern.md)
- [Architecture Decision: Separate Query Pattern](../architecture-decisions/0002-separate-query-pattern.md)
- [Architecture Decision: Domain Invariants in Actions](../architecture-decisions/0003-domain-invariants-in-actions.md)
- [Architecture Decision: Action Composition for Complex Processes](../architecture-decisions/0004-action-composition-for-complex-processes.md)
- [AGENTS.md](../../AGENTS.md) - Development guidelines for agentic coding
