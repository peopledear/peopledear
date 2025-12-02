<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffRequest;

use App\Enums\PeopleDear\TimeOffType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateTimeOffRequestData extends Data
{
    public function __construct(
        public readonly int $organization_id,
        public readonly int $employee_id,
        public readonly string $period_id,
        public readonly TimeOffType $type,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.v\Z')]
        public readonly CarbonImmutable $start_date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.v\Z')]
        public readonly ?CarbonImmutable $end_date,
        public readonly bool $is_half_day,
    ) {}
}
