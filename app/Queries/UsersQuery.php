<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class UsersQuery
{
    /**
     * @return Builder<User>
     */
    public function builder(): Builder
    {
        return User::query()
            ->with('role')->latest();
    }
}
