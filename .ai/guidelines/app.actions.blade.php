# App/Actions guidelines

- This application uses the Action pattern and prefers for much logic to live in reusable and composable Action classes.
- Actions live in `app/Actions`, they are named based on what they do, with no suffix.
- Actions will be called from many different places: jobs, commands, HTTP requests, API requests, MCP requests, and more.
- Create dedicated Action classes for business logic with a single `handle()` method.
- Inject dependencies via constructor using private properties.
- Create new actions with `php artisan make:action "{name}" --no-interaction`
- Wrap complex operations in `DB::transaction()` within actions when multiple models are involved.
- Some actions won't require dependencies via `__construct` and they can use just the `handle()` method.

@boostsnippet('Example action class', 'php')
<?php

declare(strict_types=1);

namespace App\Actions;

final readonly class CreateFavorite
{
    public function __construct(private FavoriteService $favorites)
    {
        //
    }

    public function handle(User $user, string $favorite): bool
    {
        return $this->favorites->add($user, $favorite);
    }
}

@endboostsnippet

## Action Method Signatures

### Update Actions
** ALWAYS accept the model being updated ** as a parameter:

@boostsnippet('Update Action Signature', 'php')
<?php

// ✅ CORRECT - Accept the model
public function handle(UpdateOrganizationData $data, Organization $organization): Organization
{
    $organization->update($data->toArray());
    return $organization->refresh();
}

// ❌ WRONG - Query for the model inside
public function handle(UpdateOrganizationData $data): Organization
{
    $organization = Organization::query()->firstOrFail(); // ❌ Don't do this
    $organization->update($data->toArray());
    return $organization->refresh();
}

@endboostsnippet

### Delete Actions
** ALWAYS accept the model being deleted ** as a parameter:

@boostsnippet('Delete Action Signature', 'php')
<?php

// ✅ CORRECT - Accept the model
public function handle(Office $office): void
{
    $office->delete();
}

// ❌ WRONG - Accept ID and query
public function handle(int $officeId): void
{
    $office = Office::query()->findOrFail($officeId); // ❌ Don't do this
    $office->delete();
}

@endboostsnippet

### Create Actions
** Accept Data object and any required context ** (user, parent models, etc .):

@boostsnippet('Create Action Signature', 'php')
<?php

public function handle(CreateOfficeData $data, Organization $organization): Office
{
    $office = $organization->offices()->create($data->toArray());

    $office->address()->create($data->address->toArray());

    return $office->refresh();
}

@endboostsnippet

## Using toArray() with Optional

** Data objects automatically handle Optional ** - use;`toArray()` for clean updates:

    @boostsnippet('toArray with Optional', 'php')
    <?php

public function handle(UpdateOrganizationData $data, Organization $organization): Organization
{
    // toArray() excludes Optional fields automatically!
    // Only fields that were provided in the request are included
    $organization->update($data->toArray());

    return $organization->refresh();
}

@endboostsnippet

## Action Naming Convention

    ** Action classes are named WITHOUT the "Action" suffix:**

-✅ CORRECT: `CreateOrganization`, `UpdateOrganization`, `DeleteOffice`
- ❌ WRONG: `CreateOrganizationAction`, `UpdateOrganizationAction`, `DeleteOfficeAction`

** Action test files follow the same naming:**

-Action class: `app/Actions/CreateOrganization.php`
- Test file: `tests/Unit/Actions/CreateOrganizationTest.php`

This keeps action names clean and concise while maintaining clarity about their purpose .

## Testing Actions

**ALWAYS create unit tests for Actions ** to verify business logic:

@boostsnippet('Action Tests', 'php')
<?php

use App\Actions\UpdateOrganization;
use App\Data\PeopleDear\Organization\UpdateOrganizationData;
use App\Models\Organization;
use Spatie\LaravelData\Optional;

test('it updates organization with all fields', function (): void {
    $action = new UpdateOrganization();

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Name',
        'phone' => 'Old Phone',
    ]);

    $data = UpdateOrganizationData::from([
        'name' => 'New Name',
        'vat_number' => 'VAT123',
        'ssn' => 'SSN123',
        'phone' => '+1234567890',
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('New Name')
        ->and($result->vat_number)->toBe('VAT123')
        ->and($result->ssn)->toBe('SSN123')
        ->and($result->phone)->toBe('+1234567890');
});

test('it updates organization with partial fields only', function (): void {
    $action = new UpdateOrganization();

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Old Name',
        'phone' => '+9999999999',
        'vat_number' => 'OLD_VAT',
    ]);

    // Only update name - phone and vat_number should stay unchanged
    $data = UpdateOrganizationData::from([
        'name' => 'New Name',
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('New Name')
        ->and($result->phone)->toBe('+9999999999') // ✅ Unchanged
        ->and($result->vat_number)->toBe('OLD_VAT'); // ✅ Unchanged
});

test('it can set fields to null explicitly', function (): void {
    $action = new UpdateOrganization();

    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly([
        'name' => 'Test Company',
        'phone' => '+1234567890',
    ]);

    // Explicitly set phone to null
    $data = UpdateOrganizationData::from([
        'phone' => null,
    ]);

    $result = $action->handle($data, $organization);

    expect($result->name)->toBe('Test Company') // ✅ Unchanged
    ->and($result->phone)->toBeNull(); // ✅ Set to null
});

@endboostsnippet
