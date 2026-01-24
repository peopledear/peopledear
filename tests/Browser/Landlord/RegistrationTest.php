<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;

test('register a new organization and user', function (): void {

    visit('/register')
        ->assertSee('Create an account')
        ->fill('organization_name', 'Acme Corp')
        ->fill('name', 'John Doe')
        ->fill('email', 'john.doe@peopledear.com')
        ->fill('password', 'Password123!')
        ->fill('password_confirmation', 'Password123!')
        ->click('Create account')
        ->screenshot();

    $organization = Organization::query()
        ->where('name', 'Acme Corp')
        ->first();

    $user = User::query()
        ->where('email', 'john.doe@peopledear.com')
        ->first();

    expect($organization)->toBeInstanceOf(Organization::class)
        ->name->toBe('Acme Corp')
        ->and($user)->toBeInstanceOf(User::class)
        ->name->toBe('John Doe')
        ->organization_id->toBe($organization->id)
        ->and($user->hasRole(UserRole::Owner));

});
