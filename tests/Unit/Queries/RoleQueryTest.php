<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Queries\RoleQuery;
use Spatie\Permission\Models\Role;

test('returns the people_manager role', function (): void {

    $role = new RoleQuery()
        ->withRole(UserRole::PeopleManager)
        ->builder()
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('people_manager');

});
