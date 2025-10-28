<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use App\Enums\Integrations\OpenHolidays\OpenHolidaysHolidayType;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysRegionalScope;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysTemporalScope;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

final class OpenHolidaysHolidayData extends Data
{
    /**
     * @param  Collection<int, OpenHolidaysLocalizedTextData>  $name
     * @param  null|Collection<int, OpenHolidaysSubdivisionReferenceData>  $subdivisions
     */
    public function __construct(
        public readonly string $id,
        #[DataCollectionOf(OpenHolidaysLocalizedTextData::class)]
        public readonly Collection $name,
        #[WithCast(EnumCast::class, OpenHolidaysHolidayType::class)]
        public readonly OpenHolidaysHolidayType $type,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly CarbonImmutable $startDate,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly CarbonImmutable $endDate,
        public readonly bool $nationwide,
        #[WithCast(EnumCast::class, OpenHolidaysRegionalScope::class)]
        public readonly ?OpenHolidaysRegionalScope $regionalScope = null,
        public readonly ?Collection $subdivisions = null,
        #[WithCast(EnumCast::class, OpenHolidaysTemporalScope::class)]
        public readonly ?OpenHolidaysTemporalScope $temporalScope = null,
        public readonly ?string $comment = null,

    ) {}

}
