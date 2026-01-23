<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use App\Enums\CarryOverType;
use App\Enums\PeopleDear\RecurringPeriod;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Transformers\EnumTransformer;

#[MapName(SnakeCaseMapper::class)]
final class TimeOffTypeBalanceConfigData extends Data
{
    public function __construct(
        public ?int $accrualDaysPerYear = null,
        #[WithCast(EnumCast::class, CarryOverType::class)]
        #[WithTransformer(EnumTransformer::class, CarryOverType::class)]
        public null|CarryOverType|int $carryOverType = null,
        public ?int $carryOverDaysLimit = null,
        public ?int $carryOverExpiryMonths = null,
        #[WithCast(EnumCast::class, RecurringPeriod::class)]
        #[WithTransformer(EnumTransformer::class, RecurringPeriod::class)]
        public null|RecurringPeriod|int $recurringPeriod = null,
        public ?int $limitPerPeriod = null,
    ) {}

}
