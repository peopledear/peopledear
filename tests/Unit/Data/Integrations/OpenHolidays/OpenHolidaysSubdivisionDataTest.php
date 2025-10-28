<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;

test('creates data with all fields', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
            'es' => 'California',
        ],
        'officialLanguages' => 'en',
        'children' => [
            ['isoCode' => 'US-CA-LA', 'name' => 'Los Angeles'],
        ],
    ]);

    expect($data->isoCode)
        ->toBe('US-CA')
        ->and($data->shortName)
        ->toBe('CA')
        ->and($data->name)
        ->toBe([
            'en' => 'California',
            'es' => 'California',
        ])
        ->and($data->officialLanguages)
        ->toBe('en')
        ->and($data->children)
        ->toBe([
            ['isoCode' => 'US-CA-LA', 'name' => 'Los Angeles'],
        ]);
});

test('creates data with required fields only', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
        ],
    ]);

    expect($data->isoCode)
        ->toBe('US-CA')
        ->and($data->shortName)
        ->toBe('CA')
        ->and($data->name)
        ->toBe(['en' => 'California'])
        ->and($data->officialLanguages)
        ->toBeNull()
        ->and($data->children)
        ->toBeNull();
});

test('getLocalizedName returns name in specified language', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'name' => [
            'en' => 'Bavaria',
            'de' => 'Bayern',
            'es' => 'Baviera',
        ],
    ]);

    expect($data->getLocalizedName('de'))
        ->toBe('Bayern')
        ->and($data->getLocalizedName('es'))
        ->toBe('Baviera')
        ->and($data->getLocalizedName('en'))
        ->toBe('Bavaria');
});

test('getLocalizedName falls back to english when language not found', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
            'es' => 'California',
        ],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('California');
});

test('getLocalizedName falls back to first available language when english not found', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'name' => [
            'de' => 'Bayern',
            'es' => 'Baviera',
        ],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('Bayern');
});

test('getLocalizedName falls back to shortName when no names available', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [],
    ]);

    expect($data->getLocalizedName('en'))
        ->toBe('CA');
});

test('getLocalizedName uses default language from config when none specified', function (): void {
    config()->set('openholidays.default_language', 'de');

    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'name' => [
            'en' => 'Bavaria',
            'de' => 'Bayern',
        ],
    ]);

    expect($data->getLocalizedName())
        ->toBe('Bayern');
});

test('toArray returns correct structure with all fields', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
        ],
        'officialLanguages' => 'en',
        'children' => [
            ['isoCode' => 'US-CA-LA', 'name' => 'Los Angeles'],
        ],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['isoCode', 'shortName', 'name', 'officialLanguages', 'children'])
        ->and($array['isoCode'])
        ->toBe('US-CA')
        ->and($array['shortName'])
        ->toBe('CA')
        ->and($array['name'])
        ->toBe(['en' => 'California'])
        ->and($array['officialLanguages'])
        ->toBe('en')
        ->and($array['children'])
        ->toBe([
            ['isoCode' => 'US-CA-LA', 'name' => 'Los Angeles'],
        ]);
});

test('toArray returns correct structure with required fields only', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
        ],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['isoCode', 'shortName', 'name'])
        ->and($array['isoCode'])
        ->toBe('US-CA')
        ->and($array['shortName'])
        ->toBe('CA')
        ->and($array['name'])
        ->toBe(['en' => 'California']);
});

test('handles nested children structure', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'name' => [
            'en' => 'California',
        ],
        'children' => [
            [
                'isoCode' => 'US-CA-LA',
                'name' => ['en' => 'Los Angeles'],
                'children' => [
                    ['isoCode' => 'US-CA-LA-DOWNTOWN', 'name' => ['en' => 'Downtown']],
                ],
            ],
        ],
    ]);

    expect($data->children)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($data->children[0])
        ->toHaveKey('children')
        ->and($data->children[0]['children'])
        ->toBeArray()
        ->toHaveCount(1);
});
