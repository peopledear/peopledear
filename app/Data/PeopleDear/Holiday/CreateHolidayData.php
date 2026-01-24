<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Holiday;

use App\Enums\HolidayType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class CreateHolidayData extends Data
{
    public function __construct(
        public string $organizationId,
        public readonly CarbonImmutable $date,
        public readonly string $name,
        public HolidayType $type,
        public bool $nationwide,
        public readonly string $countryIsoCode,
        public readonly string $apiHolidayId,
        public readonly ?string $subdivisionCode = null,
        public readonly bool $isCustom = false
    ) {}

}
