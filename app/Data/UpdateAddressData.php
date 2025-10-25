<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateAddressData extends Data
{
    public function __construct(
        public readonly string|Optional $line1,
        public readonly string|Optional|null $line2,
        public readonly string|Optional $city,
        public readonly string|Optional|null $state,
        public readonly string|Optional $postal_code,
        public readonly string|Optional $country,
    ) {}
}
