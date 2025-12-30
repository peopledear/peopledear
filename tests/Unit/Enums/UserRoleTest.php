<?php

declare(strict_types=1);

use App\Enums\UserRole;

test('has the correct user roles', function (): void {

    $roles = UserRole::cases();

    $rolesArray = array_map(fn (UserRole $role) => $role->value, $roles);

    expect($rolesArray)
        ->toBe([
            'employee',
            'manager',
            'owner',
            'people_manager',
        ]);

});

test('has correct descriptions for each role', function (): void {

    expect(UserRole::Employee->description())
        ->toBe('Employee with standard access')
        ->and(UserRole::Manager->description())
        ->toBe('Manages team members, approves time-offs and overtime requests')
        ->and(UserRole::Owner->description())
        ->toBe('Full access to all organization settings, employees, time-offs, and reports')
        ->and(UserRole::PeopleManager->description())
        ->toBe('People Manager with specific HR access');

});
