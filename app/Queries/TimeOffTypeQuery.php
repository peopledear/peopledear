<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\TimeOffType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class TimeOffTypeQuery
{
    private Builder $builder;

    public function __invoke(): self
    {
        $this->builder = TimeOffType::query();

        return $this;
    }

    /**
     * @return Builder<TimeOffType>
     */
    public function make(): Builder
    {
        return $this->builder;
    }

    /**
     * @return Collection<TimeOffType>
     */
    public function get(): Collection
    {
        return $this->builder->get();
    }

    public function active(): self
    {
        $this->builder
            ->where('is_active', true);

        return $this;
    }
}
