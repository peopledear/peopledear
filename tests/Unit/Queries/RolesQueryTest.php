<?php

declare(strict_types=1);

use App\Models\Role;
use App\Queries\RolesQuery;
use Illuminate\Database\Eloquent\Builder;

test('it returns a query builder instance', function () {
    $query = new RolesQuery();

    expect($query->builder())->toBeInstanceOf(Builder::class);
});

test('it orders roles by name ascending', function () {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $managerRole = Role::query()->where('name', 'manager')->first();

    $query = new RolesQuery();
    $roles = $query->builder()->get();

    expect($roles->first()->name)->toBe('admin')
        ->and($roles->get(1)->name)->toBe('employee')
        ->and($roles->last()->name)->toBe('manager');
});

test('it returns all roles', function () {
    $query = new RolesQuery();
    $roles = $query->builder()->get();

    expect($roles->count())->toBeGreaterThanOrEqual(3);
});
