<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\PeopleDear\TimeOffStatus;
use App\Enums\PeopleDear\TimeOffType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateTimeOffData extends Data
{
    public function __construct(
        public readonly TimeOffType|Optional $type,
        public readonly TimeOffStatus|Optional $status,
        public readonly CarbonImmutable|Optional $start_date,
        public readonly CarbonImmutable|Optional|null $end_date,
        public readonly bool|Optional $is_half_day,
    ) {}
}
