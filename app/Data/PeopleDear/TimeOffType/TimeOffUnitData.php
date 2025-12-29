<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use Spatie\LaravelData\Data;

final class TimeOffUnitData extends Data
{
    public function __construct(
        public readonly int $value,
        public readonly string $label,
    ) {}

}
