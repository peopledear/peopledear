<?php

declare(strict_types=1);

namespace App\Actions\CountrySubdivision;

use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Models\CountrySubdivision;

final readonly class CreateCountrySubdivision
{
    public function handle(CreateCountrySubdivisionData $data): CountrySubdivision
    {
        $attributes = $data->except('children')->toArray();

        /** @var CountrySubdivision $countrySubdivision */
        $countrySubdivision = CountrySubdivision::query()
            ->updateOrCreate(
                ['iso_code' => $data->isoCode],
                $attributes
            )
            ->fresh();

        return $countrySubdivision;
    }
}
