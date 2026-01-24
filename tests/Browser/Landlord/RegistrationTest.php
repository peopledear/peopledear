<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;

test('register a new organization and user',
    /**
     * @throws Throwable
     */
    function (): void {

        visit('/register')
            ->assertSee('Create an account')
            ->assertNoJavascriptErrors()
            ->fill('organization_name', 'Acme Corp')
            ->fill('name', 'John Doe')
            ->fill('email', 'john.doe@peopledear.com')
            ->fill('password', 'Password123!')
            ->fill('password_confirmation', 'Password123!')
            ->press('Create account')
            ->wait(3);

        /** @var Organization $organization */
        $organization = Organization::query()
            ->where('name', 'Acme Corp')
            ->firstOrFail();

        /** @var User $user */
        $user = User::query()
            ->where('email', 'john.doe@peopledear.com')
            ->firstOrFail();

        expect($organization)->toBeInstanceOf(Organization::class)
            ->name->toBe('Acme Corp')
            ->identifier->toBe('acme-corp')
            ->and($user)->toBeInstanceOf(User::class)
            ->name->toBe('John Doe')
            ->organization_id->toBe($organization->id)
            ->and($user->hasRole(UserRole::Owner))->toBeTrue();

        // Note: After registration, the app redirects to the tenant subdomain (acme-corp.localhost/org)
        // Pest browser tests cannot switch hosts mid-test, so tenant page verification
        // is handled in tests/Browser/Tenant/ with proper host configuration.
    });
