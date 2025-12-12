<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeopleDear\SystemRole;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

final class RoleQuery
{
    private ?SystemRole $role = null;

    /**
     * @return Builder<Role>
     */
    public function builder(): Builder
    {
        $query = Role::query();

        if ($this->role instanceof SystemRole) {
            $query->where('name', $this->role);
        }

        return $query;
    }

    public function withRole(SystemRole $role): self
    {
        $this->role = $role;

        return $this;
    }
}
