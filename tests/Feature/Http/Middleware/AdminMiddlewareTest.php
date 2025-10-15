<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows admin users to access admin routes', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    actingAs($admin)
        ->get('/admin/test')
        ->assertOk()
        ->assertJson(['message' => 'Admin access granted']);
});

it('denies manager users access to admin routes', function () {
    $manager = User::factory()
        ->manager()
        ->create();

    actingAs($manager)
        ->get('/admin/test')
        ->assertForbidden();
});

it('denies employee users access to admin routes', function () {
    $employee = User::factory()
        ->employee()
        ->create();

    actingAs($employee)
        ->get('/admin/test')
        ->assertForbidden();
});

it('denies users without roles access to admin routes', function () {
    $user = User::factory()
        ->create(['role_id' => null]);

    actingAs($user)
        ->get('/admin/test')
        ->assertForbidden();
});

it('denies unauthenticated users access to admin routes', function () {
    get('/admin/test')
        ->assertRedirect('/login');
});

it('denies inactive admin users access to admin routes', function () {
    $admin = User::factory()
        ->admin()
        ->inactive()
        ->create();

    actingAs($admin)
        ->get('/admin/test')
        ->assertForbidden();
});
