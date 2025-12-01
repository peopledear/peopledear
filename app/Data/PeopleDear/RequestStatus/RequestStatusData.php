<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\RequestStatus;

use Spatie\LaravelData\Data;

final class RequestStatusData extends Data
{
    public function __construct(
        public readonly int $status,
        public readonly string $label,
        public readonly string $icon,
        public readonly string $color,
    ) {}

}
