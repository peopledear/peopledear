<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\CountrySubdivision;

use App\Enums\PeopleDear\CountrySubdivisionType;
use App\Transformers\ArrayableToJsonTransformer;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class CreateCountrySubdivisionData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  array<int, string>  $officialLanguages
     */
    public function __construct(
        public readonly int $countryId,
        public readonly ?int $countrySubdivisionId,
        #[WithTransformer(ArrayableToJsonTransformer::class)]
        public readonly array $name,
        public readonly string $code,
        public readonly string $isoCode,
        public readonly string $shortName,
        public readonly CountrySubdivisionType $type,
        #[WithTransformer(ArrayableToJsonTransformer::class)]
        public readonly array $officialLanguages,
    ) {}

}
