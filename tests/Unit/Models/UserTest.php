<?php

declare(strict_types=1);

use App\Enums\Disk;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('avatar returns null when no avatar',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly(['avatar' => null]);

        expect($user->avatarUrl)->toBeNull();
    });

test('avatar returns route when avatar exists',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var User $user */
        $user = User::factory()->createQuietly();

        Storage::fake(Disk::S3Public->value);

        $user->update([
            'avatar' => 'avatars/'.$user->id.'.webp',
        ]);

        expect($user->avatar)
            ->not->toBeNull()
            ->toContain('/avatars/')
            ->toContain($user->id);
    });

test('user has organizations relationship', function (): void {
    $organization = Organization::factory()
        ->create();

    /** @var User $user */
    $user = User::factory()
        ->for($organization)
        ->create();

    expect($user->organization())
        ->toBeInstanceOf(BelongsTo::class)
        ->and($user->organization->id)
        ->toBe($organization->id);
});

test('user has roles relationship', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    expect($user->roles())->toBeInstanceOf(BelongsToMany::class);
});

test('user has permissions relationship', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    expect($user->permissions())->toBeInstanceOf(BelongsToMany::class);
});

test('user roles relationship is properly loaded', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Role $role */
    $role = Role::create(['name' => 'user']);

    $user->assignRole($role);
    $user->load('roles');

    expect($user->roles)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->first()
        ->name->toBe('user');
});

test('user permissions relationship is properly loaded', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Permission $permission */
    $permission = Permission::create(['name' => 'employees.view']);

    $user->givePermissionTo($permission);
    $user->load('permissions');

    expect($user->permissions)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->first()
        ->name->toBe('employees.view');
});

test('to array', function (): void {
    /** @var User $user */
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'avatar',
        ]);
});
