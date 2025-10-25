<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\OfficeType;
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
