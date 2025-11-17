<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

use function App\organization;

final class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $organization = organization();

        if (! $organization instanceof Organization) {
            return;
        }

        $builder->whereHas('organization', function (Builder $query) use ($organization): void {
            $query->where('id', $organization->id);
        });
    }
}
