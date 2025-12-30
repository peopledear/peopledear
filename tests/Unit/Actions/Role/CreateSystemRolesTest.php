<?php

declare(strict_types=1);

use App\Actions\Role\CreateSystemRoles;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {

    $this->action = resolve(CreateSystemRoles::class);

});

test('create system role successfully',
    /**
     * @throws Throwable
     */
    function (): void {

        $this->action->handle();

        $permissionNames = Role::findByName(UserRole::PeopleManager->value)
            ->getPermissionNames();

        expect($permissionNames)
            ->toContain(
                UserPermission::TimeOffTypeManage->value,
                UserPermission::TimeOffTypeCreate->value
            );

    });
