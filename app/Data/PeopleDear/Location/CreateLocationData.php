<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Location;

use App\Data\PeopleDear\Address\CreateAddressData;
use App\Enums\LocationType;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class CreateLocationData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly LocationType $type,
        public readonly string $countryId,
        public readonly ?string $phone,
        public readonly CreateAddressData $address,
    ) {}
}
