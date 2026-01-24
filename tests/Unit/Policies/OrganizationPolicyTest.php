<?php

declare(strict_types=1);

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserRole;
use App\Models\User;
use App\Policies\OrganizationPolicy;
use Illuminate\Support\Facades\Artisan;

beforeEach(function (): void {
    // Initialize system roles and permissions
    Artisan::call('config:clear');
    app(CreateSystemRoles::class)->handle();

    $this->policy = new OrganizationPolicy();
});

test('view method allows users with OrganizationView permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $user->assignRole(UserRole::PeopleManager); // This role has OrganizationView permission

    expect($this->policy->view($user))->toBeTrue();
});

test('view method denies users without OrganizationView permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    // User doesn't have OrganizationView permission

    expect($this->policy->view($user))->toBeFalse();
});

test('viewAny method allows users with OrganizationManage permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $user->assignRole(UserRole::Owner); // Owner role has permissions that should work

    expect($this->policy->viewAny($user))->toBeTrue();
});

test('viewAny method denies users without OrganizationManage permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    // User doesn't have OrganizationManage permission

    expect($this->policy->viewAny($user))->toBeFalse();
});

test('update method allows users with OrganizationEdit permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $user->assignRole(UserRole::PeopleManager); // This role has OrganizationEdit permission

    expect($this->policy->update($user))->toBeTrue();
});

test('update method denies users without OrganizationEdit permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    // User doesn't have OrganizationEdit permission

    expect($this->policy->update($user))->toBeFalse();
});

test('delete method allows users with OrganizationDelete permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $user->assignRole(UserRole::Owner); // Owner has OrganizationDelete permission

    expect($this->policy->delete($user))->toBeTrue();
});

test('delete method denies users without OrganizationDelete permission', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    // User doesn't have OrganizationDelete permission

    expect($this->policy->delete($user))->toBeFalse();
});
