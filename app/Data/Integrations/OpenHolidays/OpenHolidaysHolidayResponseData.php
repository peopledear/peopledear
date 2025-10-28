<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class OpenHolidaysHolidayResponseData extends Data
{
    /**
     * @param  DataCollection<int, OpenHolidaysHolidayData>  $holidays
     */
    public function __construct(
        #[DataCollectionOf(OpenHolidaysHolidayData::class)]
        public readonly DataCollection $holidays,
    ) {}
}
