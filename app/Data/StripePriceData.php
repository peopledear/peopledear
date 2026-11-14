<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class StripePriceData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $product,
        public readonly int $unit_amount,
        public readonly string $currency,
        public readonly string $interval,
        public readonly bool $active,
    ) {}
}
