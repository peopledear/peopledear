<?php

declare(strict_types=1);

use App\Actions\Role\CreateSystemRoles;
use App\Enums\SessionKey;
use App\Enums\UserRole;
use App\Models\TimeOffType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    resolve(CreateSystemRoles::class)->handle();

    $this->organization = $this->tenant;

    Session::put(SessionKey::CurrentOrganization->value, $this->organization->id);

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', UserRole::PeopleManager->value)
        ->first()
        ?->fresh();

    $this->user = User::factory()->for($this->organization)->create([
        'email' => 'peoplemanager@test.test',
    ]);
    $this->user->assignRole($peopleManagerRole);

    TimeOffType::factory()
        ->for($this->organization)
        ->count(4)
        ->create();

});

test('can navigate back to the create time off type page', function (): void {
    Auth::login($this->user);

    visit(route(
        name: 'tenant.settings.time-off-types.create',
        parameters: ['tenant' => $this->organization->identifier],
        absolute: false
    ))
        ->click('Back')
        ->assertSee('Create Time Off Type');
});

test('renders the create page', function (): void {
    Auth::login($this->user);

    visit(route(
        name: 'tenant.settings.time-off-types.create',
        parameters: ['tenant' => $this->organization->identifier],
        absolute: false
    ))
        ->assertSee('Create Time Off Type');

});
