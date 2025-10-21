<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->admin = User::factory()
        ->admin()
        ->create();
});

test('admin can view users index page', function (): void {
    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('users')
                ->has('pendingInvitations')
                ->has('roles')
        );
});

test('non-admin cannot access users page', function (): void {
    $employee = User::factory()
        ->employee()
        ->create();

    actingAs($employee)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('manager cannot access users page', function (): void {
    $manager = User::factory()
        ->manager()
        ->create();

    actingAs($manager)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('unauthenticated user cannot access users page', function (): void {
    get(route('admin.users.index'))
        ->assertRedirect(route('auth.login.index'));
});

test('users page displays paginated users', function (): void {
    User::factory()->count(20)->create();

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('users.data', 15)
                ->where('users.per_page', 15)
        );
});

test('users page displays users with roles', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $employee = User::factory()->create([
        'role_id' => $employeeRole->id,
        'created_at' => now()->addSecond(), // Ensure it's the newest
    ]);

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('users.data', fn (Assert $users) => $users
                    ->where('0.id', $employee->id)
                    ->has('0.role')
                    ->where('0.role.name', 'employee')
                    ->etc()
                )
        );
});

test('users page displays pending invitations', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    $invitation = Invitation::factory()->create([
        'email' => 'pending@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(7),
    ]);

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('pendingInvitations', 1)
                ->where('pendingInvitations.0.email', 'pending@example.com')
                ->has('pendingInvitations.0.role')
                ->has('pendingInvitations.0.inviter')
        );
});

test('users page does not display accepted invitations', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    Invitation::factory()->create([
        'email' => 'accepted@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => now(),
        'expires_at' => now()->addDays(7),
    ]);

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('pendingInvitations', 0)
        );
});

test('users page does not display expired invitations', function (): void {
    $role = Role::query()->where('name', 'employee')->first();
    Invitation::factory()->create([
        'email' => 'expired@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('pendingInvitations', 0)
        );
});

test('users page displays all available roles', function (): void {
    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->has('roles', 3)
        );
});

test('users are ordered by created_at descending', function (): void {
    $olderUser = User::factory()->create(['created_at' => now()->subDays(2)]);
    $newerUser = User::factory()->create(['created_at' => now()->addSecond()]);

    actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Members')
                ->where('users.data.0.id', $newerUser->id)
        );
});

test('inactive admin cannot access users page', function (): void {
    $inactiveAdmin = User::factory()
        ->admin()
        ->inactive()
        ->create();

    actingAs($inactiveAdmin)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});
