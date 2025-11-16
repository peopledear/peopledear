<?php

declare(strict_types=1);

use App\Enums\PeopleDear\UserRole;
use App\Models\Organization;
use App\Models\User;

test('renders the employee overview page', function (): void {
    Organization::factory()
        ->createQuietly();

    $user = User::factory()
        ->createQuietly();

    $user->assignRole(UserRole::Employee);

    $response = $this->actingAs($user)
        ->get(route('employee.overview'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('employee-overview/index')
            ->has('employee'));
});
