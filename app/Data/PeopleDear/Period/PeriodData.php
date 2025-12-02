<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Period;

use App\Enums\PeopleDear\PeriodStatus;
use DateTimeInterface;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

final class PeriodData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly int $organizationId,
        public readonly int $year,
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'Y-m-d')]
        public readonly DateTimeInterface $start,
        #[WithTransformer(DateTimeInterfaceTransformer::class, 'Y-m-d')]
        public readonly DateTimeInterface $end,
        #[WithCast(EnumCast::class, PeriodStatus::class)]
        public readonly PeriodStatus $status,
    ) {}

}
