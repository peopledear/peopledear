<?php

declare(strict_types=1);

namespace App\Actions\Location;

use App\Actions\Address\UpdateAddress;
use App\Data\PeopleDear\Location\UpdateLocationData;
use App\Enums\LocationType;
use App\Exceptions\Domain\LocationAlreadyExistsException;
use App\Models\Location;
use App\Queries\LocationQuery;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;
use Throwable;

final readonly class UpdateLocation
{
    public function __construct(
        private UpdateAddress $updateAddress,
        private LocationQuery $locationQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Location $location, UpdateLocationData $data): Location
    {
        $this->validateHeadquartersPerCountry($location, $data);

        return DB::transaction(function () use ($location, $data): Location {
            $location->update($data->except('address')->toArray());

            if (! ($data->address instanceof Optional)) {
                $this->updateAddress->handle($location, $data->address);
            }

            return $location->refresh();
        });
    }

    private function validateHeadquartersPerCountry(
        Location $location,
        UpdateLocationData $data
    ): void {
        $isChangingToHeadquarters = ($data->type instanceof LocationType)
            && ($data->type === LocationType::Headquarters)
            && ($location->type !== LocationType::Headquarters);

        $isChangingCountryForHeadquarters =
            ($location->type === LocationType::Headquarters)
            && ! ($data->countryId instanceof Optional)
            && ($data->countryId !== $location->country_id);

        if (! $isChangingToHeadquarters && ! $isChangingCountryForHeadquarters) {
            return;
        }

        $countryId = ($data->countryId instanceof Optional)
            ? $location->country_id
            : $data->countryId;

        $exists = ($this->locationQuery)()
            ->ofOrganization($location->organization_id)
            ->ofType(LocationType::Headquarters)
            ->ofCountry($countryId)
            ->except($location)
            ->exists();

        if ($exists) {
            throw LocationAlreadyExistsException::headquartersInCountry(
                $location->organization->name,
                $countryId
            );
        }
    }
}
