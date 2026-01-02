<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

final readonly class OrganizationQuery
{
    /**
     * @var Builder<Organization>
     */
    private Builder $builder;

    public function __construct()
    {
        $this->builder = Organization::query();

    }

    /**
     * @return Builder<Organization>
     */
    public function builder(): Builder
    {
        return $this->builder;
    }

    public function identifier(string $identifier): self
    {

        $this->builder
            ->where('identifier', $identifier);

        return $this;
    }

    public function exists(): bool
    {
        return $this->builder
            ->exists();
    }
}
