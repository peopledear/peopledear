<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Location;

use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Enums\LocationType;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class UpdateLocationData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly LocationType|Optional $type,
        public readonly string|Optional $countryId,
        public readonly string|Optional|null $phone,
        public readonly UpdateAddressData|Optional $address,
    ) {}
}
