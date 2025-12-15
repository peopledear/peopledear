<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffRequest;

use App\Enums\PeopleDear\RequestStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class UpdateTimeOffRequestData extends Data
{
    public function __construct(
        public readonly string|Optional $timeOffTypeId,
        public readonly RequestStatus|Optional $status,
        public readonly CarbonImmutable|Optional $startDate,
        public readonly CarbonImmutable|Optional|null $endDate,
        public readonly bool|Optional $isHalfDay,
    ) {}
}
