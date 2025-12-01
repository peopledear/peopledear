<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Period;
use Illuminate\Database\Eloquent\Builder;

final class PeriodQuery
{
    /**
     * @return Builder<Period>
     */
    public function builder(): Builder
    {
        return Period::query();
    }

    /**
     * @return Builder<Period>
     */
    public function active(): Builder
    {
        return $this->builder()
            ->where('status', PeriodStatus::Active);
    }
}
