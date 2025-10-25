<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class CreateAddressData extends Data
{
    public function __construct(
        public readonly string $line1,
        public readonly ?string $line2,
        public readonly string $city,
        public readonly ?string $state,
        public readonly string $postal_code,
        public readonly string $country,
    ) {}
}
