<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use Spatie\LaravelData\Data;

final class TimeOffTypeData extends Data
{
    public function __construct(
        public readonly int $type,
        public readonly string $label,
        public readonly string $icon,
        public readonly string $color,
    ) {}

}
