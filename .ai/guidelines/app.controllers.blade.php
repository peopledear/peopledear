# Controller Guidelines

## Controller Responsibilities

Controllers are **thin HTTP adapters** that:
1. Validate requests (via Form Requests)
2. Convert validated data to Data objects
3. Call Actions to perform business logic
4. Return responses (Inertia renders, redirects, JSON)

Controllers should **NOT** contain business logic - that belongs in Actions.

## Structure

### Flat Hierarchy
- **Controllers live directly in `app/Http/Controllers/`** - NO nested folders
- Clear naming eliminates need for namespace nesting
- Examples: `UserController`, `OfficeController`, `OrganizationController`

### Single vs Multi-Action Controllers

**Single Action Controllers** - Use `__invoke()` for one specific action:
```php
final readonly class ActivateUserController
{
public function __invoke(User $user, ActivateUserAction $action): RedirectResponse
{
$action->handle($user);
return redirect()->back();
}
}
```

**Multi-Action Controllers** - Use named methods for related CRUD operations:
```php
final readonly class OfficeController
{
public function store(CreateOfficeRequest $request): RedirectResponse { }
public function update(UpdateOfficeRequest $request, Office $office): RedirectResponse { }
public function destroy(Office $office): RedirectResponse { }
}
```

## Request Validation

### Always Use Form Requests
**ALWAYS create dedicated Form Request classes** - NEVER use inline validation:

@boostsnippet('Form Request Example')
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'address' => ['required', 'array'],
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['required', 'string', 'max:255'],
            'address.country' => ['required', 'string', 'max:255'],
        ];
    }
}

```

### Create Form Requests
```bash
php artisan make:request UpdateOfficeRequest--no - interaction
```

## Controller Flow Pattern

@boostsnippet('Complete Controller Example')
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOffice;
use App\Actions\DeleteOffice;
use App\Actions\UpdateOffice;
use App\Data\PeopleDear\Office\CreateOfficeData;
use App\Data\PeopleDear\Office\UpdateOfficeData;
use App\Http\Requests\CreateOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class OfficeController
{
    public function store(
        CreateOfficeRequest $request,
        CreateOffice        $action,
        #[CurrentUser] User $user
    ): RedirectResponse
    {
        $data = CreateOfficeData::from($request->validated());

        $action->handle($data, $user);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Office created successfully');
    }

    public function update(
        UpdateOfficeRequest $request,
        Office              $office,
        UpdateOffice        $action
    ): RedirectResponse
    {
        $data = UpdateOfficeData::from($request->validated());

        $action->handle($data, $office);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Office updated successfully');
    }

    public function destroy(
        Office       $office,
        DeleteOffice $action
    ): RedirectResponse
    {
        $action->handle($office);

        return redirect()
            ->route('admin.settings.organization.edit')
            ->with('success', 'Office deleted successfully');
    }
}

```

## Dependency Injection

### Method-Level Injection
**ALWAYS inject Actions at the method level** - NOT in `__construct()`:

✅ **CORRECT - Method-level injection:**
```php
public function store(
    CreateOfficeRequest $request,
    CreateOffice        $action,  // ✅ Injected here
    #[CurrentUser] User $user
): RedirectResponse
{
    $data = CreateOfficeData::from($request->validated());
    $action->handle($data, $user);
    return redirect()->route('admin.settings.organization.edit');
}
```

❌ **WRONG - Constructor injection:**
```php
public function __construct(
    private CreateOffice $createOffice,  // ❌ Don't do this
)
{
}

public function store(CreateOfficeRequest $request): RedirectResponse
{
    $this->createOffice->handle(...);  // ❌ Wrong pattern
}
```

### Use Laravel 12 Contextual Attributes

**Always use `#[CurrentUser]` instead of `Request::user()`:**

@boostsnippet('CurrentUser Attribute')
```php

public function store(
    CreateOfficeRequest $request,
    #[CurrentUser] User $user  // ✅ Clean and explicit
): RedirectResponse
{
    $data = CreateOfficeData::from($request->validated());
    $this->createOffice->handle($data, $user);
    return redirect()->route('admin.settings.organization.edit');
}
```

❌ **Don't inject Request just to get user:**
```php
public function store(
    CreateOfficeRequest $request,
    Request             $httpRequest  // ❌ Unnecessary
): RedirectResponse
{
    $user = $httpRequest->user();  // ❌ Verbose
    // ...
}
```

## Return Types

- **Inertia Pages** - Return `Response` (from `Inertia::render()`)
- **Redirects** - Return `RedirectResponse`
- **JSON APIs** - Return `JsonResponse`
- **Always use explicit return type hints**

## What NOT to Put in Controllers

❌ **Business Logic** - Belongs in Actions
❌ **Database Queries** - Belongs in Actions/Queries
❌ **Validation Logic** - Belongs in Form Requests
❌ **Data Transformation** - Belongs in Actions/Data objects

✅ **What Controllers SHOULD Do:**
- Type-hint Form Requests
- Create Data objects from validated data
- Call Actions
- Return HTTP responses

## Example: Wrong vs Right

❌ **WRONG - Business logic in controller:**
```php
public function update(UpdateOfficeRequest $request, Office $office): RedirectResponse
{
    // ❌ Business logic in controller
    $office->update([
        'name' => $request->validated('name'),
        'type' => $request->validated('type'),
    ]);

    // ❌ More business logic
    if ($request->has('address')) {
        $office->address->update($request->validated('address'));
    }

    return redirect()->back();
}
```

✅ **CORRECT - Delegate to Action:**
```php
public function update(
    UpdateOfficeRequest $request,
    Office              $office,
    UpdateOffice        $action
): RedirectResponse
{
    // ✅ Create Data object from validated data
    $data = UpdateOfficeData::from($request->validated());

    // ✅ Delegate business logic to Action
    $action->handle($data, $office);

    // ✅ Return response
    return redirect()
        ->route('admin.settings.organization.edit')
        ->with('success', 'Office updated');
}
```
