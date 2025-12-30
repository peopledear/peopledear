<?php

declare(strict_types=1);

namespace App\Actions\Role;

use App\Enums\UserRole;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;

final readonly class CreateRole
{
    public function handle(string|UserRole $roleName, string $guard = 'web'): RoleContract
    {

        $name = $roleName instanceof UserRole ? $roleName->value : $roleName;

        try {
            return Role::create([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        } catch (RoleAlreadyExists) {
            return Role::findByName($name, $guard);
        }

    }
}
