<?php

declare(strict_types=1);

namespace App\Actions\Permission;

use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission;

final readonly class CreatePermission
{
    public function handle(string $permissionName, string $guard = 'web'): PermissionContract
    {
        return Permission::create([
            'name' => $permissionName,
            'guard_name' => $guard,
        ]);
    }
}
