<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Adapters;

use App\Contracts\HolidayAdapter;
use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayData;
use App\Data\PeopleDear\Holiday\CreateHolidayData;
use Carbon\CarbonImmutable;

/**
 * @implements HolidayAdapter<OpenHolidaysHolidayData>
 */
final class OpenHolidaysHolidayAdapter implements HolidayAdapter
{
    /**
     * @param  OpenHolidaysHolidayData  $data
     */
    public function toCreateData(mixed $data, int $organizationId): CreateHolidayData
    {
        $localizedName = $data->name->where('language', 'PT')->first();
        $subdivision = $data->subdivisions?->first();

        return new CreateHolidayData(
            organizationId: $organizationId,
            date: CarbonImmutable::parse($data->startDate),
            name: $localizedName->text ?? 'Unknown',
            type: $data->type->transform(),
            nationwide: $data->nationwide,
            countryIsoCode: 'PT',
            apiHolidayId: $data->id,
            subdivisionCode: $subdivision?->code,
            isCustom: false,
        );
    }
}
