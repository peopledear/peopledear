<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffRequest;

use App\Data\CastsAndTransformers\RequestStatusTransformer;
use App\Data\PeopleDear\Period\PeriodData;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeData;
use App\Enums\PeopleDear\RequestStatus;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(CamelCaseMapper::class)]
#[MapInputName(SnakeCaseMapper::class)]
final class TimeOffRequestData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly string $employeeId,
        public readonly TimeOffTypeData $type,
        #[WithCast(EnumCast::class, RequestStatus::class)]
        #[WithTransformer(RequestStatusTransformer::class)]
        public readonly RequestStatus $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $startDate,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?string $endDate,
        public readonly bool $isHalfDay,
        public readonly PeriodData $period,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $createdAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $updatedAt,
    ) {}

}
