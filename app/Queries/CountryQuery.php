<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

final class CountryQuery
{
    /**
     * @return Builder<Country>
     */
    public function builder(): Builder
    {
        return Country::query();
    }
}
