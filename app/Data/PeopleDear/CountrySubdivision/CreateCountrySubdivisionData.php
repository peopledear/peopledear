<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\CountrySubdivision;

use App\Enums\PeopleDear\CountrySubdivisionType;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(SnakeCaseMapper::class)]
final class CreateCountrySubdivisionData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  string[]  $officialLanguages
     * @param  Collection<int, CreateCountrySubdivisionData>|null  $children
     */
    public function __construct(
        public readonly string $countryId,
        public readonly ?string $countrySubdivisionId,
        public readonly array $name,
        public readonly string $code,
        public readonly string $isoCode,
        public readonly string $shortName,
        public readonly CountrySubdivisionType $type,
        public readonly array $officialLanguages,
        public readonly ?Collection $children = null,
    ) {}

}
