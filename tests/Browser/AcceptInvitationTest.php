<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    // Create roles using firstOrCreate to avoid unique constraint errors
    $this->employeeRole = Role::query()->firstOrCreate(
        ['name' => 'employee'],
        ['display_name' => 'Employee', 'description' => 'Can submit requests']
    );

    $adminRole = Role::query()->firstOrCreate(
        ['name' => 'admin'],
        ['display_name' => 'Administrator', 'description' => 'Full system access']
    );

    // Create admin user to invite others
    $this->admin = User::factory()->create([
        'role_id' => $adminRole->id,
    ]);
});

it('user can access invitation link', function (): void {
    $invitation = Invitation::factory()
        ->pending()
        ->create([
            'email' => 'invited@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    $page->assertSee('invited@example.com')
        ->assertSee($this->employeeRole->display_name)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

it('user can fill registration form and create account', function (): void {
    $invitation = Invitation::factory()
        ->pending()
        ->create([
            'email' => 'newemployee@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    $page->fill('name', 'John Doe')
        ->fill('password', 'SecurePassword123!')
        ->fill('password_confirmation', 'SecurePassword123!')
        ->click('Create Account')
        ->waitForText('Dashboard');

    // Verify user was created
    $user = User::query()
        ->where('email', 'newemployee@example.com')
        ->first();

    expect($user)
        ->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->role_id)->toBe($this->employeeRole->id)
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('SecurePassword123!', $user->password))->toBeTrue();

    // Verify invitation was marked as accepted
    $invitation->refresh();

    expect($invitation->accepted_at)->not->toBeNull();
});

it('user is redirected to dashboard after registration', function (): void {
    $invitation = Invitation::factory()
        ->pending()
        ->create([
            'email' => 'newuser@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    $page->fill('name', 'Jane Smith')
        ->fill('password', 'MyPassword456!')
        ->fill('password_confirmation', 'MyPassword456!')
        ->click('Create Account')
        ->waitForText('Dashboard');

    // User should be authenticated
    expect(Auth::check())->toBeTrue();

    $user = User::query()
        ->where('email', 'newuser@example.com')
        ->first();

    expect(Auth::id())->toBe($user->id);
});

it('validation errors are displayed correctly', function (): void {
    $invitation = Invitation::factory()
        ->pending()
        ->create([
            'email' => 'testuser@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    // Submit form without filling required fields
    $page->click('Create Account')
        ->waitForText('name');

    // Should show validation errors
    $page->assertSee('required');
});

it('expired invitation shows error', function (): void {
    $invitation = Invitation::factory()
        ->expired()
        ->create([
            'email' => 'expired@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    // Should see error message about expired invitation
    $page->assertSee('410');
});

it('accepted invitation cannot be used again', function (): void {
    $invitation = Invitation::factory()
        ->accepted()
        ->create([
            'email' => 'alreadyaccepted@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    // Should see 404 error since invitation was already accepted
    $page->assertSee('404');
});

it('password confirmation must match', function (): void {
    $invitation = Invitation::factory()
        ->pending()
        ->create([
            'email' => 'mismatch@example.com',
            'role_id' => $this->employeeRole->id,
            'invited_by' => $this->admin->id,
        ]);

    $page = visit("/invitation/{$invitation->token}");

    $page->fill('name', 'Test User')
        ->fill('password', 'Password123!')
        ->fill('password_confirmation', 'DifferentPassword456!')
        ->click('Create Account')
        ->wait(1);

    // Should show validation error about password confirmation
    $page->assertSee('password');
});
