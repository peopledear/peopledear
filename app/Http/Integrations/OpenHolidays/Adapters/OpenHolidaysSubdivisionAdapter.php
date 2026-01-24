<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Adapters;

use App\Contracts\Adapter;
use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;
use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\CountrySubdivisionType;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysSubdivisionType;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

/**
 * @implements Adapter<OpenHolidaysSubdivisionData,CreateCountrySubdivisionData>
 */
final readonly class OpenHolidaysSubdivisionAdapter implements Adapter
{
    /**
     * @param  string[]  $countryLanguages
     *
     * @throws Throwable
     */
    public function toCreateData(
        mixed $data,
        ?string $countryId = null,
        array $countryLanguages = []
    ): CreateCountrySubdivisionData {

        throw_unless($countryId, InvalidArgumentException::class, 'countryId is required');

        $categoryText = $data->category !== [] && isset($data->category[0]['text'])
            ? $data->category[0]['text']
            : '';

        $type = OpenHolidaysSubdivisionType::tryFrom($categoryText)?->transform()
            ?? CountrySubdivisionType::District;

        $name = [];
        foreach ($data->name as $nameItem) {
            $name[$nameItem['language']] = $nameItem['text'];
        }

        $officialLanguages = $this->parseOfficialLanguages(
            $data->officialLanguages,
            $countryLanguages
        );

        $children = $this->transformChildren(
            $data->children,
            $countryId,
            $countryLanguages
        );

        return new CreateCountrySubdivisionData(
            countryId: $countryId,
            countrySubdivisionId: null,
            name: $name,
            code: $data->code,
            isoCode: $data->code,
            shortName: $data->shortName,
            type: $type,
            officialLanguages: $officialLanguages,
            children: $children,
        );
    }

    /**
     * @param  array<int, string>|null  $subdivisionLanguages
     * @param  string[]  $countryLanguages
     * @return string[]
     */
    private function parseOfficialLanguages(
        ?array $subdivisionLanguages,
        array $countryLanguages
    ): array {
        if ($subdivisionLanguages !== null && $subdivisionLanguages !== []) {
            return $subdivisionLanguages;
        }

        return $countryLanguages;
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $children
     * @param  string[]  $countryLanguages
     * @return Collection<int, CreateCountrySubdivisionData>|null
     */
    private function transformChildren(
        ?array $children,
        string $countryId,
        array $countryLanguages
    ): ?Collection {
        if ($children === null || $children === []) {
            return null;
        }

        return collect($children)
            ->map(function (array $child) use ($countryId, $countryLanguages): CreateCountrySubdivisionData {
                $childData = OpenHolidaysSubdivisionData::from($child);

                return $this->toCreateData(
                    $childData,
                    countryId: $countryId,
                    countryLanguages: $countryLanguages
                );
            });
    }
}
