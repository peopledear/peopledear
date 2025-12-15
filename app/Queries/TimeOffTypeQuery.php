<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\TimeOffType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class TimeOffTypeQuery
{
    /**
     * @var Builder<TimeOffType>
     */
    private Builder $builder;

    public function __invoke(?string $id = null): self
    {
        $this->builder = TimeOffType::query();

        if ($id) {
            $this->builder
                ->where('id', $id);
        }

        return $this;
    }

    /**
     * @return Builder<TimeOffType>
     */
    public function make(): Builder
    {
        return $this->builder;
    }

    public function first(): ?TimeOffType
    {
        return $this->builder->first();
    }

    /**
     * @return Collection<int, TimeOffType>
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
