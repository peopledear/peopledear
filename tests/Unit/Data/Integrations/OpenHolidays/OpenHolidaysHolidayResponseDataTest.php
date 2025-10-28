<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayData;
use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayResponseData;
use App\Data\Integrations\OpenHolidays\OpenHolidaysLocalizedTextData;
use App\Enums\Integrations\OpenHolidays\OpenHolidaysHolidayType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\DataCollection;

test('creates data with holidays collection', function (): void {
    $data = OpenHolidaysHolidayResponseData::from([
        'holidays' => [
            [
                'id' => 'us-new-year',
                'name' => [
                    ['language' => 'en', 'text' => "New Year's Day"],
                ],
                'type' => 'Public',
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-01',
                'nationwide' => true,
            ],
            [
                'id' => 'us-independence',
                'name' => [
                    ['language' => 'en', 'text' => 'Independence Day'],
                ],
                'type' => 'Public',
                'startDate' => '2025-07-04',
                'endDate' => '2025-07-04',
                'nationwide' => true,
            ],
        ],
    ]);

    expect($data->holidays)
        ->toBeInstanceOf(DataCollection::class)
        ->toHaveCount(2)
        ->and($data->holidays->first())
        ->toBeInstanceOf(OpenHolidaysHolidayData::class);
});

test('creates data with empty holidays collection', function (): void {
    $data = OpenHolidaysHolidayResponseData::from([
        'holidays' => [],
    ]);

    expect($data->holidays)
        ->toBeInstanceOf(DataCollection::class)
        ->toBeEmpty();
});

test('holidays collection contains correct holiday data', function (): void {
    $data = OpenHolidaysHolidayResponseData::from([
        'holidays' => [
            [
                'id' => 'us-new-year',
                'name' => [
                    ['language' => 'en', 'text' => "New Year's Day"],
                    ['language' => 'es', 'text' => 'Año Nuevo'],
                ],
                'type' => 'Public',
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-01',
                'nationwide' => true,
            ],
        ],
    ]);

    /** @var OpenHolidaysHolidayData $holiday */
    $holiday = $data->holidays->first();

    expect($holiday->id)
        ->toBe('us-new-year')
        ->and($holiday->name)
        ->toHaveCount(2)
        ->and($holiday->name->first())
        ->toBeInstanceOf(OpenHolidaysLocalizedTextData::class)
        ->and($holiday->type)
        ->toBe(OpenHolidaysHolidayType::Public)
        ->and($holiday->startDate)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($holiday->startDate->format('Y-m-d'))
        ->toBe('2025-01-01')
        ->and($holiday->nationwide)
        ->toBeTrue();
});

test('toArray returns correct structure', function (): void {
    $data = OpenHolidaysHolidayResponseData::from([
        'holidays' => [
            [
                'id' => 'us-new-year',
                'name' => [
                    ['language' => 'en', 'text' => "New Year's Day"],
                ],
                'type' => 'Public',
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-01',
                'nationwide' => true,
            ],
        ],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKey('holidays')
        ->and($array['holidays'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($array['holidays'][0])
        ->toHaveKeys(['id', 'name', 'type', 'startDate', 'endDate', 'nationwide'])
        ->and($array['holidays'][0]['id'])
        ->toBe('us-new-year');
});

test('can iterate over holidays', function (): void {
    $data = OpenHolidaysHolidayResponseData::from([
        'holidays' => [
            [
                'id' => 'us-new-year',
                'name' => [['language' => 'en', 'text' => "New Year's Day"]],
                'type' => 'Public',
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-01',
                'nationwide' => true,
            ],
            [
                'id' => 'us-independence',
                'name' => [['language' => 'en', 'text' => 'Independence Day']],
                'type' => 'Public',
                'startDate' => '2025-07-04',
                'endDate' => '2025-07-04',
                'nationwide' => true,
            ],
        ],
    ]);

    $count = 0;
    foreach ($data->holidays as $holiday) {
        expect($holiday)->toBeInstanceOf(OpenHolidaysHolidayData::class);
        $count++;
    }

    expect($count)->toBe(2);
});
