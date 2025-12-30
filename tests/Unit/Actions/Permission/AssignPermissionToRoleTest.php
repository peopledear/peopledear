<?php

declare(strict_types=1);

use App\Actions\Permission\AssignPermissionToRole;
use App\Actions\Permission\CreatePermission;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->action = resolve(AssignPermissionToRole::class);
    $this->createPermissonAction = resolve(CreatePermission::class);
});

test('assign permission to role',
    /**
     * @throws Throwable
     */
    function (): void {

        $role = Role::findByName(UserRole::PeopleManager->value);
        $permission = $this->createPermissonAction->handle(UserPermission::TimeOffTypeManage);

        $this->action->handle($role, $permission);

        expect($role->hasPermissionTo($permission->name))
            ->toBeTrue();

    });
