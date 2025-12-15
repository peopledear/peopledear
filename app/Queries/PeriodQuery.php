<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Period;
use Illuminate\Database\Eloquent\Builder;

final class PeriodQuery
{
    /** @var Builder<Period> */
    private Builder $builder;

    public function __invoke(?string $id = null): self
    {
        $this->builder = Period::query();

        if ($id) {
            $this->builder
                ->where('id', $id);
        }

        return $this;
    }

    /**
     * @return Builder<Period>
     */
    public function make(): Builder
    {
        return $this->builder;
    }

    public function first(): ?Period
    {
        return $this->builder->first();
    }

    public function active(): self
    {
        $this->builder
            ->where('status', PeriodStatus::Active);

        return $this;
    }
}
