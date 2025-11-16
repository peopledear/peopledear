<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\PeopleDear\TimeOffType;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

final class CreateTimeOffData extends Data
{
    public function __construct(
        public readonly int $organization_id,
        public readonly int $employee_id,
        public readonly TimeOffType $type,
        public readonly Carbon $start_date,
        public readonly ?Carbon $end_date,
        public readonly bool $is_half_day,
    ) {}
}
