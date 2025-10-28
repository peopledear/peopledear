<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysCountryData;

test('creates data with all fields', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'en' => 'United States',
            'es' => 'Estados Unidos',
            'de' => 'Vereinigte Staaten',
        ],
        'officialLanguages' => ['en', 'es'],
    ]);

    expect($data->isoCode)
        ->toBe('US')
        ->and($data->name)
        ->toBe([
            'en' => 'United States',
            'es' => 'Estados Unidos',
            'de' => 'Vereinigte Staaten',
        ])
        ->and($data->officialLanguages)
        ->toBe(['en', 'es']);
});

test('getLocalizedName returns name in specified language', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'en' => 'United States',
            'es' => 'Estados Unidos',
            'de' => 'Vereinigte Staaten',
        ],
        'officialLanguages' => ['en'],
    ]);

    expect($data->getLocalizedName('es'))
        ->toBe('Estados Unidos')
        ->and($data->getLocalizedName('de'))
        ->toBe('Vereinigte Staaten')
        ->and($data->getLocalizedName('en'))
        ->toBe('United States');
});

test('getLocalizedName falls back to english when language not found', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'en' => 'United States',
            'es' => 'Estados Unidos',
        ],
        'officialLanguages' => ['en'],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('United States');
});

test('getLocalizedName falls back to first available language when english not found', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'es' => 'Estados Unidos',
            'de' => 'Vereinigte Staaten',
        ],
        'officialLanguages' => ['es'],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('Estados Unidos');
});

test('getLocalizedName falls back to isoCode when no names available', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [],
        'officialLanguages' => ['en'],
    ]);

    expect($data->getLocalizedName('en'))
        ->toBe('US');
});

test('getLocalizedName uses default language from config when none specified', function (): void {
    config()->set('openholidays.default_language', 'es');

    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'en' => 'United States',
            'es' => 'Estados Unidos',
        ],
        'officialLanguages' => ['en', 'es'],
    ]);

    expect($data->getLocalizedName())
        ->toBe('Estados Unidos');
});

test('toArray returns correct structure', function (): void {
    $data = OpenHolidaysCountryData::from([
        'isoCode' => 'US',
        'name' => [
            'en' => 'United States',
        ],
        'officialLanguages' => ['en'],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['isoCode', 'name', 'officialLanguages'])
        ->and($array['isoCode'])
        ->toBe('US')
        ->and($array['name'])
        ->toBe(['en' => 'United States'])
        ->and($array['officialLanguages'])
        ->toBe(['en']);
});
