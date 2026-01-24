<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\TimeOffType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {

    $this->user = User::factory()
        ->for($this->tenant)
        ->create([
            'email' => 'peoplemanager@test.test',
        ]);
    $this->user->assignRole(UserRole::PeopleManager);

    TimeOffType::factory()
        ->for($this->tenant)
        ->count(4)
        ->create();

});

test('can navigate back to the create time off type page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        Auth::login($this->user);

        visit(tenant_route(
            name: 'tenant.settings.time-off-types.create',
            tenant: $this->tenant,
            absolute: false,
        ))
            ->click('Back')
            ->assertSee('Create Time Off Type');
    });

test('renders the create page', function (): void {
    Auth::login($this->user);

    visit(route(
        name: 'tenant.settings.time-off-types.create',
        parameters: ['tenant' => $this->tenant->identifier],
        absolute: false
    ))
        ->assertSee('Create Time Off Type');

});
