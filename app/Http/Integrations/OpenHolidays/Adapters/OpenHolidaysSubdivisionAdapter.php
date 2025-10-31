<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Adapters;

use App\Contracts\Adapter;
use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;
use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysSubdivisionType;
use App\Enums\PeopleDear\CountrySubdivisionType;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

/**
 * Adapter for transforming OpenHolidays subdivision data to internal format.
 *
 * Handles recursive transformation of nested subdivision hierarchies,
 * official language parsing and inheritance, and type mapping.
 *
 * @implements Adapter<OpenHolidaysSubdivisionData,CreateCountrySubdivisionData>
 */
final readonly class OpenHolidaysSubdivisionAdapter implements Adapter
{
    /**
     * Transform OpenHolidays subdivision data to CreateCountrySubdivisionData.
     *
     * Supports both array context (for Adapter interface) and named parameters
     * (for direct usage and backward compatibility).
     *
     * @param  OpenHolidaysSubdivisionData  $data  The subdivision data from OpenHolidays API
     * @param  int|null  $countryId  Country ID when using named parameters
     * @param  array<int, string>  $countryLanguages  Country languages when using named parameters
     * @return CreateCountrySubdivisionData The transformed internal subdivision data
     *
     * @throws Throwable
     */
    public function toCreateData(
        mixed $data,
        ?int $countryId = null,
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
     * Parse official languages from API data or inherit from country.
     *
     * When subdivision has no specific official languages defined,
     * inherits from the parent country's official languages.
     *
     * @param  array<int, string>|null  $subdivisionLanguages  Subdivision's official languages from API
     * @param  array<int, string>  $countryLanguages  Country's official languages as fallback
     * @return array<int, string> Array of official language codes
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
     * Recursively transform nested subdivision children.
     *
     * Maintains hierarchy structure by recursively transforming
     * all nested subdivision levels.
     *
     * @param  array<int, array<string, mixed>>|null  $children  Child subdivisions from API
     * @param  int  $countryId  Parent country ID to pass to children
     * @param  array<int, string>  $countryLanguages  Country languages to pass to children
     * @return Collection<int, CreateCountrySubdivisionData>|null Transformed child subdivisions
     */
    private function transformChildren(
        ?array $children,
        int $countryId,
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
