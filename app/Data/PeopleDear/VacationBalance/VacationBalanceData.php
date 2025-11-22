<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\VacationBalance;

use App\Data\CastsAndTransformers\ToNumberOfDaysTransformer;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapOutputName(CamelCaseMapper::class)]
final class VacationBalanceData extends Data
{
    public function __construct(
        public readonly int $year,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $fromLastYear,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $accrued,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $taken,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $remaining,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $lastYearBalance,
        #[WithCast(ToNumberOfDaysTransformer::class)]
        public readonly string $yearBalance
    ) {}

}
