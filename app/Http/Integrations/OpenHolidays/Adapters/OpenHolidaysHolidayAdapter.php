<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Adapters;

use App\Contracts\HolidayAdapter;
use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayData;
use App\Data\PeopleDear\Holiday\CreateHolidayData;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Throwable;

/**
 * @implements HolidayAdapter<OpenHolidaysHolidayData, CreateHolidayData>
 */
final class OpenHolidaysHolidayAdapter implements HolidayAdapter
{
    /**
     * @param  OpenHolidaysHolidayData  $data
     *
     * @throws Throwable
     */
    public function toCreateData(mixed $data, ?int $organizationId = null): CreateHolidayData
    {

        throw_unless($organizationId, InvalidArgumentException::class, 'organizationId is required in context');

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
