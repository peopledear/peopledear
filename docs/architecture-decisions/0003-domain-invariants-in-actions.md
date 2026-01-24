# ADR-0003: Domain Invariants in Actions

## Status

Accepted

## Context

Domain invariants are rules that must always be true for your system. Examples:

- Organization identifiers must be unique
- Time off requests cannot be approved if employee has insufficient balance
- User email must be unique within an organization

We need to decide where to enforce these rules.

### Potential Locations

1. **Database Constraints**: UNIQUE indexes, foreign keys
2. **Model Mutators**: `saving()`, `creating()` events
3. **Form Requests**: Validation rules
4. **Actions**: Business logic layer

### Issues with Other Approaches

```php
// BAD: Business logic in Model
class Organization extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            $organization->identifier = Str::slug($organization->name);
        });
    }
}
```

Problems:

- Hard to test in isolation
- Cannot inject dependencies
- Mixed concerns (model should be lean)
- Not reusable across different contexts

```php
// BAD: Business logic only in Form Request
class CreateOrganizationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'unique:organizations'],
        ];
    }
}
```

Problems:

- Rules only enforced via HTTP
- Not enforced via Commands, Jobs, or Tests
- Business logic coupled to HTTP layer

## Decision

Enforce all **domain invariant rules** in Action classes.

### Pattern Rules

1. **Location**: All domain invariants live in Actions
2. **Responsibility**: Actions are the single source of truth for business rules
3. **Enforcement**:
    - Actions check invariants before state changes
    - Throw exceptions when invariants are violated
    - Use Query classes to read data for validation
4. **Validation vs Invariants**:
    - Form Request: Input validation (format, required fields)
    - Action: Domain invariants (business rules)

### Example: Unique Organization Identifier

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
        $identifier = Str::slug($name);

        // Domain invariant: Organization identifier must be unique
        $exists = $this->organizationQuery
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

### Example: Time Off Request Business Rules

```php
declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;use App\Enums\RequestStatus;use App\Models\Employee;use App\Models\TimeOffRequest;use App\Models\TimeOffType;use App\Queries\CurrentVacationBalanceQuery;use Illuminate\Support\Facades\DB;

final readonly class CreateTimeOffRequest
{
    public function __construct(
        private CurrentVacationBalanceQuery $balanceQuery,
    ) {}

    public function handle(
        CreateTimeOffRequestData $data,
        Employee $employee,
        TimeOffType $timeOffType
    ): TimeOffRequest {
        return DB::transaction(function () use ($data, $timeOffType, $employee): TimeOffRequest {
            // Domain invariant: Check sufficient balance for auto-approved requests
            if (! $timeOffType->requires_approval) {
                $balance = $this->balanceQuery->execute($employee);

                if ($balance < $data->days) {
                    throw new InsufficientBalanceException(
                        'Insufficient vacation balance. Available: '.$balance.', Requested: '.$data->days
                    );
                }
            }

            // Domain invariant: Set status based on approval requirement
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

### Example: Status Transition Rules

```php
declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Enums\RequestStatus;use App\Models\TimeOffRequest;

final readonly class ApproveTimeOffRequest
{
    public function handle(TimeOffRequest $timeOffRequest): TimeOffRequest
    {
        // Domain invariant: Can only approve pending requests
        if ($timeOffRequest->status !== RequestStatus::Pending) {
            throw new InvalidStatusTransitionException(
                'Cannot approve time off request with status: '.$timeOffRequest->status->value
            );
        }

        $timeOffRequest->update([
            'status' => RequestStatus::Approved,
        ]);

        return $timeOffRequest->refresh();
    }
}
```

## Consequences

### Positive

- **Single Source of Truth**: All business rules in one place
- **Testability**: Invariants tested with Actions
- **Consistency**: Rules enforced everywhere (HTTP, CLI, Jobs, Tests)
- **Injectability**: Actions can use Query classes, services, etc.
- **Clear Separation**: Form Request validates input, Action enforces rules

### Negative

- **More Complexity**: Need to understand validation vs invariants
- **Potential Duplication**: May need both Form Request and Action checks (mitigated by clear separation of concerns)

### Risks

- **Inconsistent Enforcement**: Developers might forget to use Actions (mitigated by code reviews and AGENTS.md guidelines)
- **Performance**: Multiple queries for validation (mitigated by caching, database indexing)

## Comparison: Validation vs Domain Invariants

| Aspect       | Form Request Validation       | Action Domain Invariants         |
| ------------ | ----------------------------- | -------------------------------- |
| Purpose      | Input format, required fields | Business rules                   |
| Examples     | Email format, password length | Unique email, sufficient balance |
| Location     | HTTP layer only               | Business logic layer             |
| Reusability  | HTTP only                     | Everywhere                       |
| Dependencies | None                          | Can inject Queries, services     |

### Form Request Example

```php
class CreateOrganizationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Validation: Input format and presence
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
            'vat_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
```

### Action Example

```php
final readonly class CreateOrganization
{
    public function __construct(
        private MakeOrganizationIdentifier $makeOrganizationIdentifier,
        private MakeOrganizationResourceKey $makeOrganizationResourceKey,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {
        // Domain invariant: Ensure unique identifier
        $data->additional([
            'identifier' => $this->makeOrganizationIdentifier->handle($data->name),
            'resource_key' => $this->makeOrganizationResourceKey->handle(),
        ]);

        $organization = Organization::query()->create($data->toArray());

        return $organization->refresh();
    }
}
```

## Related Decisions

- [ADR-0001: Use Action Pattern for Business Logic](0001-use-action-pattern.md)
- [ADR-0002: Separate Query Pattern for Data Retrieval](0002-separate-query-pattern.md)
- [ADR-0004: Action Composition for Complex Processes](0004-action-composition-for-complex-processes.md)

## References

- [Domain-Driven Design by Eric Evans](https://www.domainlanguage.com/ddd/) - Domain invariants concept
- [Clean Code by Robert C. Martin](https://www.amazon.com/Clean-Code-Handbook-Software-Craftsmanship/dp/0132350882) - Single Responsibility Principle
- [Action Pattern documentation](../developer-guide/actions-pattern.md)
