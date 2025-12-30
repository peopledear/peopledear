<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use App\Enums\UserPermission;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;

final readonly class CreatePermission
{
    public function handle(string|UserPermission $permission, string $guard = 'web'): PermissionContract
    {
        $name = $permission instanceof UserPermission ? $permission->value : $permission;

        try {
            return Permission::create([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        } catch (PermissionAlreadyExists) {
            return Permission::findByName($name, $guard);
        }

    }
}
