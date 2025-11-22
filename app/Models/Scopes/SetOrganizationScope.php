<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

final class SetOrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // This scope is for setting organization_id on creating, not for querying
    }

    /**
     * Extend the query builder with the creating event.
     *
     * @param  Builder<Model>  $builder
     */
    public function extend(Builder $builder): void
    {
        $builder->getModel()::creating(function (Model $model): void {
            /** @var int|null $currentOrgId */
            $currentOrgId = $model->getAttribute('organization_id');

            if ($currentOrgId === null) {
                /** @var int|null $organizationId */
                $organizationId = Session::get('current_organization');
                $model->setAttribute('organization_id', $organizationId);
            }
        });
    }
}
