<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\TimeOffRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;

final class LatestUserTimeOffRequestsQuery
{
    private int $count = 5;

    public function __construct(
        #[CurrentUser] private readonly ?User $user,
    ) {}

    /**
     * @return Builder<TimeOffRequest>
     */
    public function builder(): Builder
    {
        return TimeOffRequest::query()
            ->where('employee_id', $this->user?->employee?->id)
            ->limit($this->count)
            ->latest();
    }

    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
