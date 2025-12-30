<?php

declare(strict_types=1);

namespace App\Actions\Role;

use App\Actions\Permission\AssignPermissionToRole;
use App\Actions\Permission\CreatePermission;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Throwable;

use function array_key_exists;

final class CreateSystemRoles
{
    /**
     * @var array<string, Permission>
     */
    private array $permissionsCache = [];

    public function __construct(
        private readonly CreateRole $createRole,
        private readonly CreatePermission $createPermission,
        private readonly AssignPermissionToRole $assignPermissionToRole,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $roles = UserRole::cases();

        DB::transaction(function () use ($roles): void {

            foreach ($roles as $role) {
                $systemRole = $this->createRole->handle(
                    roleName: $role,
                );

                $permissions = $role->permissions();

                foreach ($permissions as $permission) {

                    if (! array_key_exists($permission->value, $this->permissionsCache)) {
                        $systemPermission = $this->createPermission->handle($permission);
                        $this->permissionsCache[$permission->value] = $systemPermission;
                    }

                    $this->assignPermissionToRole
                        ->handle(
                            $systemRole,
                            $this->permissionsCache[$permission->value]
                        );

                }

            }

        });

    }
}
