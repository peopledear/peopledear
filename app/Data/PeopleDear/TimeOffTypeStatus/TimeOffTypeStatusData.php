<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffTypeStatus;

use Spatie\LaravelData\Data;

final class TimeOffTypeStatusData extends Data
{
    public function __construct(
        public readonly int $value,
        public readonly string $label,
        public readonly string $color,
    ) {}
}
