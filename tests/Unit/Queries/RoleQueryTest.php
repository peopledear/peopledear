<?php

declare(strict_types=1);

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserRole;
use App\Queries\RoleQuery;
use Spatie\Permission\Models\Role;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        resolve(CreateSystemRoles::class)->handle();
    });

test('selects from the correct table', function (): void {

    $query = new RoleQuery();

    $sql = $query()
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain(sprintf('from "%s"', new Role()->getTable()));

});

test('scopes by role using byRole method', function (): void {

    $query = new RoleQuery();

    $sql = $query()
        ->byRole(UserRole::PeopleManager)
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain('"name" = \'people_manager\'');

});

test('scopes by string name using byName method', function (): void {

    $query = new RoleQuery();

    $sql = $query()
        ->byName('employee')
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain('"name" = \'employee\'');

});

test('filters directly via __invoke with UserRole enum', function (): void {

    $query = new RoleQuery();

    $sql = $query(UserRole::Manager)
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain('"name" = \'manager\'');

});

test('filters directly via __invoke with string name', function (): void {

    $query = new RoleQuery();

    $sql = $query('owner')
        ->builder()
        ->toRawSql();

    expect($sql)
        ->toContain('"name" = \'owner\'');

});

test('returns the people_manager role using byRole', function (): void {

    $role = new RoleQuery()()
        ->byRole(UserRole::PeopleManager)
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('people_manager');

});

test('returns role using byName method', function (): void {

    $role = new RoleQuery()()
        ->byName('employee')
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('employee');

});

test('returns role using __invoke with UserRole', function (): void {

    $role = new RoleQuery()(UserRole::Owner)
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('owner');

});

test('returns role using __invoke with string', function (): void {

    $role = new RoleQuery()('manager')
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('manager');

});

test('returns multiple roles with get method', function (): void {

    $query = new RoleQuery();

    $roles = $query()
        ->get();

    expect($roles)
        ->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);

});

test('withRole method still works for backward compatibility', function (): void {

    $role = new RoleQuery()()
        ->withRole(UserRole::PeopleManager)
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('people_manager');

});

test('resolves from container and filters by role', function (): void {

    /** @var RoleQuery $query */
    $query = resolve(RoleQuery::class);

    $role = $query(UserRole::Owner)
        ->first();

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->and($role->name)
        ->toBe('owner');

});
