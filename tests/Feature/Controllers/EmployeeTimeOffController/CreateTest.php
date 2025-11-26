<?php

declare(strict_types=1);

use App\Enums\PeopleDear\UserRole;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;

beforeEach(function (): void {
    $this->organization = Organization::factory()->createQuietly();

    $this->user = User::factory()->createQuietly();

    $this->employee = Employee::factory()
        ->for($this->organization)
        ->for($this->user)
        ->createQuietly();

    $this->user->assignRole(UserRole::Employee);
});

test('renders the create time off request page', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('employee.time-offs.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-time-offs/create')
            ->has('employee')
            ->has('types')
        );
});

test('unauthenticated user is redirected to login', function (): void {
    $response = $this->get(route('employee.time-offs.create'));

    $response->assertRedirect(route('login'));
});
