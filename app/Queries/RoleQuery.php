<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\UserRole;
use Deprecated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

final class RoleQuery
{
    /**
     * @var Builder<Role>
     */
    private Builder $builder;

    public function __invoke(UserRole|string|null $filter = null): self
    {
        $this->builder = Role::query();

        if ($filter !== null) {
            if ($filter instanceof UserRole) {
                $this->byRole($filter);
            } else {
                $this->byName($filter);
            }
        }

        return $this;
    }

    /**
     * @return Builder<Role>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }

    public function first(): ?Role
    {
        return $this->builder->first();
    }

    /**
     * @return Collection<int, Role>
     */
    public function get(): Collection
    {
        return $this->builder->get();
    }

    public function byRole(UserRole $role): self
    {
        $this->builder->where('name', $role->value);

        return $this;
    }

    public function byName(string $name): self
    {
        $this->builder->where('name', $name);

        return $this;
    }

    #[Deprecated(message: 'Use byRole() instead')]
    public function withRole(UserRole $role): self
    {
        return $this->byRole($role);
    }
}
