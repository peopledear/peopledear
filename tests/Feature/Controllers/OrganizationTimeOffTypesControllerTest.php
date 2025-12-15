<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\TimeOffType;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->organization = Organization::factory()->create();

    /** @var Role $peopleManagerRole */
    $peopleManagerRole = Role::query()
        ->where('name', 'people_manager')
        ->first()
        ?->fresh();

    $this->user = User::factory()->create();
    $this->user->assignRole($peopleManagerRole);
});

test('renders the create page', function (): void {
    TimeOffType::factory()
        ->for($this->organization)
        ->count(3)
        ->create();

    $response = $this->actingAs($this->user)
        ->get(route('org.time-off-types.create'));

    $response->assertOk();

});

test('people manager can view time off types index', function (): void {
    TimeOffType::factory()
        ->for($this->organization)
        ->count(3)
        ->create();

    $response = $this->actingAs($this->user)
        ->get(route('org.time-off-types.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-time-off-types/index')
            ->has('timeOffTypes', 3)
        );
});

test('unauthenticated user is redirected to login', function (): void {
    $response = $this->get(route('org.time-off-types.index'));

    $response->assertRedirect(route('login'));
});

test('returns empty collection when no time off types exist', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('org.time-off-types.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-time-off-types/index')
            ->has('timeOffTypes', 0)
        );
});
