<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Office;

use App\Data\PeopleDear\Address\CreateAddressData;
use App\Enums\PeopleDear\OfficeType;
use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateOfficeData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly OfficeType $type,
        public readonly ?string $phone,
        public readonly CreateAddressData $address,
    ) {}
}
