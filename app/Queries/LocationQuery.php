<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\LocationType;
use App\Models\Country;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class LocationQuery
{
    /**
     * @var Builder<Location>
     */
    private Builder $query;

    public function __invoke(): self
    {
        $this->query = Location::query();

        return clone $this;
    }

    public function ofOrganization(Organization|string $organization): self
    {
        $id = $organization instanceof Organization ? $organization->id : $organization;

        $this->query->where('organization_id', $id);

        return $this;
    }

    public function ofCountry(Country|string $country): self
    {
        $id = $country instanceof Country ? $country->id : $country;

        $this->query->where('country_id', $id);

        return $this;
    }

    public function ofType(LocationType|int $type): self
    {
        $this->query->where('type', $type);

        return $this;
    }

    public function except(Location|string $location): self
    {
        $id = $location instanceof Location ? $location->id : $location;

        $this->query->where('id', '!=', $id);

        return $this;
    }

    /**
     * @return Builder<Location>
     */
    public function builder(): Builder
    {
        return $this->query;
    }

    public function exists(): bool
    {
        return $this->query->exists();
    }

    /**
     * @return Collection<int, Location>
     */
    public function get(): Collection
    {
        return $this->query->get();
    }
}
