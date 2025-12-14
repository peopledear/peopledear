<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffRequest;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class CreateTimeOffRequestData extends Data
{
    public function __construct(
        public readonly string $organizationId,
        public readonly string $employeeId,
        public readonly string $periodId,
        public readonly string $timeOffTypeId,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.v\Z')]
        public readonly CarbonImmutable $startDate,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.v\Z')]
        public readonly ?CarbonImmutable $endDate,
        public readonly bool $isHalfDay,
    ) {}
}
