<?php

declare(strict_types=1);

use App\Enums\PeopleDear\SystemRole;
use App\Queries\RoleQuery;
use Spatie\Permission\Models\Role;

test('returns the people_manager role', function (): void {

    $role = new RoleQuery()
        ->withRole(SystemRole::PeopleManager)
        ->builder()
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('people_manager');

});
