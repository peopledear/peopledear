# ADR-0001: Use Action Pattern for Business Logic

## Status

Accepted

## Context

In Laravel applications, business logic often ends up scattered across:

- Controllers (too much logic in HTTP layer)
- Models (fat models with mixed concerns)
- Service classes (inconsistent patterns, unclear responsibilities)

This leads to:

- Difficult to test code
- Business logic duplication
- Hard to maintain as application grows
- Unclear boundaries between layers

We needed a consistent, testable pattern for encapsulating business logic.

## Decision

Use **Action classes** with a single `handle()` method to encapsulate all business logic.

### Pattern Rules

1. **Location**: All Actions live in `app/Actions/` organized by domain
2. **Structure**:
    - Mark classes as `readonly`
    - Use `declare(strict_types=1)`
    - Single `handle()` method as entry point
    - Use constructor property promotion for dependencies
3. **Naming**:
    - Use descriptive verb-noun format: `CreateOrganization`, `ApproveTimeOffRequest`
    - Do NOT use "Action" suffix
4. **Dependencies**: Inject dependencies via constructor

### Example Structure

```php
declare(strict_types=1);

namespace App\Actions\Organization;

use App\Data\PeopleDear\Organization\CreateOrganizationData;
use App\Models\Organization;

final readonly class CreateOrganization
{
    public function __construct(
        private MakeOrganizationIdentifier $makeOrganizationIdentifier,
        private MakeOrganizationResourceKey $makeOrganizationResourceKey,
    ) {}

    public function handle(CreateOrganizationData $data): Organization
    {
        $data->additional([
            'identifier' => $this->makeOrganizationIdentifier->handle($data->name),
            'resource_key' => $this->makeOrganizationResourceKey->handle(),
        ]);

        $organization = Organization::query()->create($data->toArray());

        return $organization->refresh();
    }
}
```

## Consequences

### Positive

- **Testability**: Actions are easily testable in isolation with Pest
- **Single Responsibility**: Each Action does one thing well
- **Dependency Injection**: Clear dependencies through constructor
- **Reusability**: Actions can be reused across Controllers, Commands, Jobs
- **Composability**: Actions can inject and use other Actions
- **Clear Boundaries**: Separation between HTTP, Business Logic, and Data layers

### Negative

- **More Files**: Increases number of classes in the application
- **Indirection**: Adds a layer of abstraction (though minimal)
- **Learning Curve**: Developers need to understand the pattern

### Risks

- **Over-abstraction**: Creating Actions for trivial operations (mitigated by only creating Actions when there's meaningful business logic)
- **Action Bloat**: Actions becoming too complex (mitigated by composing smaller Actions)

## Related Decisions

- [ADR-0002: Separate Query Pattern for Data Retrieval](0002-separate-query-pattern.md)
- [ADR-0003: Domain Invariants in Actions](0003-domain-invariants-in-actions.md)
- [ADR-0004: Action Composition for Complex Processes](0004-action-composition-for-complex-processes.md)

## References

- [Laravel Actions](https://github.com/spatie/laravel-actions) - Inspiration for the pattern
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Action Pattern documentation](../developer-guide/actions-pattern.md)
