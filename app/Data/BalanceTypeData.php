<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class BalanceTypeData extends Data
{
    public function __construct(
        public readonly int $value,
        public readonly string $label,
    ) {}

}
