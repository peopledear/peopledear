<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Country;

use App\Transformers\ArrayableToJsonTransformer;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class InsertCountryData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  array<int, string>  $officialLanguages
     */
    public function __construct(
        public readonly string $isoCode,
        #[WithTransformer(ArrayableToJsonTransformer::class)]
        public readonly array $name,
        #[WithTransformer(ArrayableToJsonTransformer::class)]
        public readonly array $officialLanguages,
    ) {}

}
