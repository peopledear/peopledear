<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

final class RolesQuery
{
    /**
     * @return Builder<Role>
     */
    public function builder(): Builder
    {
        return Role::query()
            ->orderBy('name', 'asc');
    }
}
