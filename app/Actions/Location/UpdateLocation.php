<?php

declare(strict_types=1);

namespace App\Actions\Location;

use App\Actions\Address\UpdateAddress;
use App\Data\PeopleDear\Location\UpdateLocationData;
use App\Enums\LocationType;
use App\Exceptions\Domain\LocationAlreadyExistsException;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;
use Throwable;

final readonly class UpdateLocation
{
    public function __construct(
        private UpdateAddress $updateAddress,
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
            && ($data->type->value === LocationType::Headquarters->value)
            && ($location->type !== LocationType::Headquarters->value);

        $isChangingCountryForHeadquarters =
            ($location->type === LocationType::Headquarters->value)
            && ! ($data->countryId instanceof Optional)
            && ($data->countryId->value !== $location->country_id);

        if (! $isChangingToHeadquarters && ! $isChangingCountryForHeadquarters) {
            return;
        }

        $query = Location::query()
            ->where('organization_id', $location->organization_id)
            ->where('type', LocationType::Headquarters->value);

        $countryId = ($data->countryId instanceof Optional)
            ? $data->countryId->value
            : $location->country_id;

        $query->where('country_id', $countryId);

        $exists = $query->where('id', '!=', $location->id)->exists();

        if ($exists) {
            throw LocationAlreadyExistsException::headquartersInCountry(
                $location->organization->name,
                $countryId
            );
        }
    }
}
