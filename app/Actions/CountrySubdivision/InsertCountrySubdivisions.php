<?php

declare(strict_types=1);

namespace App\Actions\CountrySubdivision;

use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Models\CountrySubdivision;
use Illuminate\Support\Collection;

final readonly class InsertCountrySubdivisions
{
    /**
     * @param  Collection<int, CreateCountrySubdivisionData>  $subdivisions
     */
    public function handle(Collection $subdivisions): void
    {
        CountrySubdivision::query()->upsert(
            $subdivisions->toArray(),
            ['iso_code'],
            ['country_id', 'country_subdivision_id', 'name', 'code', 'short_name', 'type', 'official_languages']
        );
    }
}
