<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

final class OrganizationQuery
{
    /**
     * @return Builder<Organization>
     */
    public function builder(): Builder
    {
        return Organization::query();
    }
}
