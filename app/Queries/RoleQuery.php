<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

final class RoleQuery
{
    private ?UserRole $role = null;

    /**
     * @return Builder<Role>
     */
    public function builder(): Builder
    {
        $query = Role::query();

        if ($this->role instanceof UserRole) {
            $query->where('name', $this->role);
        }

        return $query;
    }

    public function withRole(UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }
}
