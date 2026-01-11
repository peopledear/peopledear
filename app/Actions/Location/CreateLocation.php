<?php

declare(strict_types=1);

namespace App\Actions\Location;

use App\Actions\Address\CreateAddress;
use App\Data\PeopleDear\Location\CreateLocationData;
use App\Enums\PeopleDear\LocationType;
use App\Exceptions\Domain\LocationAlreadyExistsException;
use App\Models\Location;
use App\Models\Organization;
use App\Queries\LocationQuery;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateLocation
{
    public function __construct(
        private CreateAddress $createAddress,
        private LocationQuery $locationQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Organization $organization, CreateLocationData $data): Location
    {
        $countryHeadquartersExists = ($this->locationQuery)()
            ->ofOrganization($organization->id)
            ->ofCountry($data->countryId)
            ->ofType(LocationType::Headquarters)
            ->exists();

        if ($countryHeadquartersExists) {
            throw LocationAlreadyExistsException::headquartersInCountry(
                $organization->name,
                $data->countryId
            );
        }

        return DB::transaction(function () use ($data, $organization): Location {
            /** @var Location $location */
            $location = Location::query()->create([
                'organization_id' => $organization->id,
                ...$data->except('address')->toArray(),
            ]);

            $this->createAddress->handle($location, $data->address);

            return $location->refresh();
        });
    }
}
