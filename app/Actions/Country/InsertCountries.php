<?php

declare(strict_types=1);

namespace App\Actions\Country;

use App\Data\PeopleDear\Country\InsertCountryData;
use App\Models\Country;
use Illuminate\Support\Collection;

final readonly class InsertCountries
{
    /**
     * @param  Collection<int, InsertCountryData>  $countries
     */
    public function handle(Collection $countries): void
    {
        Country::query()->upsert(
            $countries->toArray(),
            ['iso_code'],
            ['name', 'official_languages']
        );
    }
}
