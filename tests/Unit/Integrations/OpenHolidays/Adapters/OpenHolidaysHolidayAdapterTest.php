<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayData;
use App\Data\PeopleDear\Holiday\CreateHolidayData;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysHolidayType;
use App\Http\Integrations\OpenHolidays\Adapters\OpenHolidaysHolidayAdapter;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $fixture = file_get_contents(base_path('tests/Fixtures/Saloon/OpenHolidays/portugal-public-holidays.json'));
    $data = json_decode($fixture, true);
    $this->holidays = collect(json_decode((string) $data['data'], true));
});

test('transforms OpenHolidays holiday response to CreateHolidayData',
    /**
     * @throws Throwable
     */
    function (): void {

        $openHolidaysHolidayData = OpenHolidaysHolidayData::from(
            $this->holidays
                ->where('type', OpenHolidaysHolidayType::Optional)
                ->first()
        );

        /**
         * @var OpenHolidaysHolidayAdapter $adapter
         */
        $adapter = app(OpenHolidaysHolidayAdapter::class);

        $createHolidayData = $adapter->toCreateData(
            $openHolidaysHolidayData,
            organizationId: $organizationId = Str::uuid7()->toString(),
        );

        expect($openHolidaysHolidayData)
            ->toBeInstanceOf(OpenHolidaysHolidayData::class)
            ->and($createHolidayData->name)
            ->toBe('Feriado Municipal')
            ->and($createHolidayData->organizationId)
            ->toBe($organizationId)
            ->and($createHolidayData)
            ->toBeInstanceOf(CreateHolidayData::class);

    });
