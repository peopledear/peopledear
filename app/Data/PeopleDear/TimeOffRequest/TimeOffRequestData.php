<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffRequest;

use App\Data\CastsAndTransformers\RequestStatusTransformer;
use App\Data\CastsAndTransformers\TimeOffTypeTransformer;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(CamelCaseMapper::class)]
final class TimeOffRequestData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $organizationId,
        public readonly int $employeeId,
        #[WithCast(EnumCast::class, TimeOffType::class)]
        #[WithTransformer(TimeOffTypeTransformer::class)]
        public readonly TimeOffType $type,
        #[WithCast(EnumCast::class, RequestStatus::class)]
        #[WithTransformer(RequestStatusTransformer::class)]
        public readonly RequestStatus $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $startDate,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?string $endDate,
        public readonly bool $isHalfDay,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $createdAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly string $updatedAt,
    ) {}

}
