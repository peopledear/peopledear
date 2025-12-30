<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;

final class AssignPermissionToRole
{
    public function handle(Role $role, Permission $permission): void
    {
        $permission->assignRole($role);
    }
}
