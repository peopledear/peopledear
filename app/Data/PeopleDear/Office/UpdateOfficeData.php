<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Office;

use App\Data\PeopleDear\Address\UpdateAddressData;
use App\Enums\PeopleDear\OfficeType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly OfficeType|Optional $type,
        public readonly string|Optional|null $phone,
        public readonly UpdateAddressData|Optional $address,
    ) {}
}
