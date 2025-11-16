<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;

final readonly class CurrentEmployeeQuery
{
    public function __construct(
        #[CurrentUser] private ?User $user,
    ) {}

    /**
     * @return Builder<Employee>
     */
    public function builder(): Builder
    {

        return Employee::query()
            ->where('user_id', $this->user?->id);
    }
}
