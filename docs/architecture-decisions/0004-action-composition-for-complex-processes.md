# ADR-0004: Action Composition for Complex Processes

## Status

Accepted

## Context

Many business processes in our application require multiple operations to be performed atomically. Examples:

- **CreateSystemRoles**: Creates roles, creates permissions, assigns permissions
- **CreatePeriod**: Closes previous periods, creates new period
- **OnboardingOrganization**: Creates organization, creates owner user, creates employee record, sets up default data

We need a way to handle these multi-step processes while maintaining:

- Atomicity (all or nothing)
- Reusability of individual operations
- Testability of each step
- Clear separation of concerns

### Potential Approaches

1. **Monolithic Action**: One giant Action that does everything
2. **Service Class with Multiple Methods**: A service class with private methods
3. **Action Composition**: Smaller Actions composed into a process Action

### Issues with Monolithic Approach

```php
// BAD: Monolithic Action doing everything
final readonly class CreateSystemRoles
{
    public function handle(): void
    {
        $roles = UserRole::cases();

        DB::transaction(function () use ($roles): void {
            foreach ($roles as $role) {
                // Creating role inline - not reusable
                $systemRole = Role::query()->create([
                    'name' => $role->value,
                    'guard_name' => 'web',
                ]);

                // Creating permissions inline - not reusable
                foreach ($role->permissions() as $permission) {
                    $systemPermission = Permission::query()->firstOrCreate([
                        'name' => $permission->value,
                        'guard_name' => 'web',
                    ]);

                    // Assigning permissions inline - not reusable
                    $systemRole->givePermissionTo($systemPermission);
                }
            }
        });
    }
}
```

Problems:

- Cannot test individual operations
- Cannot reuse operations independently
- Hard to understand the process flow
- Violates Single Responsibility Principle

## Decision

Use **Action composition** for complex processes. Compose smaller Actions into a process Action, wrapped in a database transaction.

### Pattern Rules

1. **Structure**:
    - Create individual Actions for each operation (e.g., `CreateRole`, `CreatePermission`, `AssignPermissionToRole`)
    - Create a process Action that composes these Actions
    - Wrap the entire process in a database transaction
2. **Injection**:
    - Inject composed Actions via constructor
    - Call `handle()` on composed Actions
3. **Atomicity**:
    - Use `DB::transaction()` for the entire process
    - If any step fails, entire process rolls back
4. **Naming**:
    - Process Actions describe the business process: `CreateSystemRoles`, `CreatePeriod`
    - Individual Actions describe the operation: `CreateRole`, `ClosePeriods`

### Example: CreateSystemRoles

```php
// Individual Action: CreateRole
declare(strict_types=1);

namespace App\Actions\Role;

use App\Enums\UserRole;
use Spatie\Permission\Models\Role;

final readonly class CreateRole
{
    public function handle(UserRole $role): Role
    {
        return Role::query()->create([
            'name' => $role->value,
            'guard_name' => 'web',
        ]);
    }
}
```

```php
// Individual Action: CreatePermission
declare(strict_types=1);

namespace App\Actions\Permission;

use App\Enums\PeopleDear\Permission;
use Spatie\Permission\Models\Permission;

final readonly class CreatePermission
{
    public function handle(Permission $permission): Permission
    {
        return Permission::query()->create([
            'name' => $permission->value,
            'guard_name' => 'web',
        ]);
    }
}
```

```php
// Individual Action: AssignPermissionToRole
declare(strict_types=1);

namespace App\Actions\Permission;

use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;

final readonly class AssignPermissionToRole
{
    public function handle(Role $role, Permission $permission): void
    {
        $role->givePermissionTo($permission);
    }
}
```

```php
// Process Action: Composes individual Actions
declare(strict_types=1);

namespace App\Actions\Role;

use App\Actions\Permission\AssignPermissionToRole;
use App\Actions\Permission\CreatePermission;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

use function array_key_exists;

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
                // Compose: Use CreateRole Action
                $systemRole = $this->createRole->handle(roleName: $role);
                $permissions = $role->permissions();

                foreach ($permissions as $permission) {
                    if (! array_key_exists($permission->value, $this->permissionsCache)) {
                        // Compose: Use CreatePermission Action
                        $systemPermission = $this->createPermission->handle($permission);
                        $this->permissionsCache[$permission->value] = $systemPermission;
                    }

                    // Compose: Use AssignPermissionToRole Action
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

### Example: CreatePeriod

```php
// Individual Action: ClosePeriods
declare(strict_types=1);

namespace App\Actions\Period;

use App\Enums\PeriodStatus;use App\Models\Organization;use App\Models\Period;

final class ClosePeriods
{
    public function handle(int $year, Organization $organization): void
    {
        Period::query()
            ->whereNot('year', $year)
            ->whereNot('status', PeriodStatus::Closed)
            ->where('organization_id', $organization->id)
            ->update(['status' => PeriodStatus::Closed]);
    }
}
```

```php
// Process Action: Composes ClosePeriods and creates new period
declare(strict_types=1);

namespace App\Actions\Period;

use App\Enums\PeriodStatus;use App\Models\Organization;use App\Models\Period;use Carbon\CarbonImmutable;use Illuminate\Support\Facades\DB;

final readonly class CreatePeriod
{
    public function __construct(
        private ClosePeriods $closePeriods,
    ) {}

    public function handle(int $year, Organization $organization): void
    {
        DB::transaction(function () use ($year, $organization): void {
            // Compose: Use ClosePeriods Action
            $this->closePeriods->handle($year, $organization);

            // Create new period
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

### Example: OnboardingOrganization (Hypothetical)

```php
// Individual Actions
final readonly class CreateOrganization { ... }
final readonly class CreateUser { ... }
final readonly class CreateEmployee { ... }
final readonly class CreateSystemTimeOffTypes { ... }

// Process Action
final readonly class OnboardingOrganization
{
    public function __construct(
        private CreateOrganization $createOrganization,
        private CreateUser $createUser,
        private CreateEmployee $createEmployee,
        private CreateSystemTimeOffTypes $createTimeOffTypes,
    ) {}

    public function handle(OnboardingData $data): array
    {
        return DB::transaction(function () use ($data): array {
            $organization = $this->createOrganization->handle($data->organization);
            $user = $this->createUser->handle($data->user, $data->password);
            $employee = $this->createEmployee->handle($data->employee, $organization);

            $this->createTimeOffTypes->handle($organization);

            return [
                'organization' => $organization,
                'user' => $user,
                'employee' => $employee,
            ];
        });
    }
}
```

## Consequences

### Positive

- **Reusability**: Individual Actions can be used independently
- **Testability**: Each Action tested in isolation
- **Atomicity**: Database transactions ensure all-or-nothing
- **Clear Separation**: Each Action has single responsibility
- **Composability**: Easy to create new processes from existing Actions
- **Maintainability**: Changes to individual steps don't affect the process

### Negative

- **More Classes**: Increases number of Action classes
- **Indirection**: More layers to understand
- **Potential Overhead**: Constructor injection of many Actions

### Risks

- **Transaction Size**: Very long transactions can cause performance issues (mitigated by keeping processes focused)
- **Action Sprawl**: Too many small Actions (mitigated by only creating Actions when there's meaningful logic)

## When to Use Action Composition

Use Action Composition when:

1. Process involves multiple distinct operations
2. Individual operations should be reusable
3. Process needs to be atomic (all or nothing)
4. Operations have meaningful business logic

Do NOT use Action Composition when:

1. Process is a single, simple operation
2. Operations are never used independently
3. There's no business logic in individual steps

## Best Practices

1. **Keep Individual Actions Focused**: Each Action should do one thing well
2. **Use Transactions Always**: Wrap composed Actions in `DB::transaction()`
3. **Cache When Appropriate**: Like permissions in `CreateSystemRoles`
4. **Clear Naming**: Process Actions describe the business process
5. **Return Meaningful Values**: Return created models or void as appropriate

## Related Decisions

- [ADR-0001: Use Action Pattern for Business Logic](0001-use-action-pattern.md)
- [ADR-0002: Separate Query Pattern for Data Retrieval](0002-separate-query-pattern.md)
- [ADR-0003: Domain Invariants in Actions](0003-domain-invariants-in-actions.md)

## References

- [Composition over Inheritance](https://en.wikipedia.org/wiki/Composition_over_inheritance)
- [Unit of Work Pattern](https://martinfowler.com/eaaCatalog/unitOfWork.html)
- [Action Pattern documentation](../developer-guide/actions-pattern.md)
