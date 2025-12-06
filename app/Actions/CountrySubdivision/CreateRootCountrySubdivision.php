<?php

declare(strict_types=1);

namespace App\Actions\CountrySubdivision;

use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Models\CountrySubdivision;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateRootCountrySubdivision
{
    public function __construct(
        private CreateCountrySubdivision $createCountrySubdivision
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(CreateCountrySubdivisionData $data): CountrySubdivision
    {
        return DB::transaction(function () use ($data): CountrySubdivision {
            $children = $data->children;

            $dataWithoutChildren = new CreateCountrySubdivisionData(
                countryId: $data->countryId,
                countrySubdivisionId: $data->countrySubdivisionId,
                name: $data->name,
                code: $data->code,
                isoCode: $data->isoCode,
                shortName: $data->shortName,
                type: $data->type,
                officialLanguages: $data->officialLanguages
            );

            $root = $this->createCountrySubdivision->handle($dataWithoutChildren);

            if ($children instanceof Collection && $children->isNotEmpty()) {
                $this->createChildren($children, $root->id, $data->countryId);
            }

            return $root;
        });
    }

    /**
     * @param  Collection<int, CreateCountrySubdivisionData>  $children
     */
    private function createChildren(Collection $children, string $parentId, string $countryId): void
    {
        foreach ($children as $childData) {
            $grandchildren = $childData->children;

            $childDataWithParent = new CreateCountrySubdivisionData(
                countryId: $countryId,
                countrySubdivisionId: $parentId,
                name: $childData->name,
                code: $childData->code,
                isoCode: $childData->isoCode,
                shortName: $childData->shortName,
                type: $childData->type,
                officialLanguages: $childData->officialLanguages
            );

            $child = $this->createCountrySubdivision->handle($childDataWithParent);

            if ($grandchildren !== null && $grandchildren->isNotEmpty()) {
                $this->createChildren($grandchildren, $child->id, $countryId);
            }
        }
    }
}
