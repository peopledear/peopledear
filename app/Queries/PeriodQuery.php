<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeriodStatus;
use App\Models\Organization;
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

        return clone $this;
    }

    /**
     * @return Builder<Period>
     */
    public function builder(): Builder
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

    public function ofOrganization(Organization|string $organizationId): self
    {
        if ($organizationId instanceof Organization) {
            $organizationId = $organizationId->id;
        }

        $this->builder
            ->where('organization_id', $organizationId);

        return $this;
    }
}
