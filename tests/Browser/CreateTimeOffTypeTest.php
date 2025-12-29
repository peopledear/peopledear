<?php

declare(strict_types=1);

use App\Enums\Support\SessionKey;
use App\Models\Organization;
use App\Models\TimeOffType;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->organization = Organization::factory()
        ->create();

    Session::put(SessionKey::CurrentOrganization->value, $this->organization->id);

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    $this->user = User::factory()->create();
    $this->user->assignRole($peopleManagerRole);

    TimeOffType::factory()
        ->for($this->organization)
        ->count(4)
        ->create();

});

test('can navigate back to the create time off type page', function (): void {
    $this->actingAs($this->user);

    visit(route('org.settings.time-off-types.create'))
        ->click('Back')
        ->assertSee('Time Off Types')
        ->assertSee('Create Time Off Type');
});

test('renders the create page', function (): void {
    $this->actingAs($this->user);

    visit(route('org.settings.time-off-types.create'))
        ->assertSee('Create Time Off Type');

});
