<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;

test('creates data with all fields', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
            ['language' => 'es', 'text' => 'California'],
        ],
        'officialLanguages' => ['en'],
        'children' => [
            [
                'code' => 'US-CA-LA',
                'isoCode' => 'US-CA-LA',
                'shortName' => 'LA',
                'category' => [['language' => 'en', 'text' => 'County']],
                'name' => [['language' => 'en', 'text' => 'Los Angeles']],
            ],
        ],
    ]);

    expect($data->code)
        ->toBe('US-CA')
        ->and($data->isoCode)
        ->toBe('US-CA')
        ->and($data->shortName)
        ->toBe('CA')
        ->and($data->category)
        ->toBe([['language' => 'en', 'text' => 'State']])
        ->and($data->name)
        ->toBe([
            ['language' => 'en', 'text' => 'California'],
            ['language' => 'es', 'text' => 'California'],
        ])
        ->and($data->officialLanguages)
        ->toBe(['en'])
        ->and($data->children)
        ->toBeArray()
        ->toHaveCount(1);
});

test('creates data with required fields only', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
        ],
    ]);

    expect($data->code)
        ->toBe('US-CA')
        ->and($data->isoCode)
        ->toBe('US-CA')
        ->and($data->shortName)
        ->toBe('CA')
        ->and($data->category)
        ->toBe([['language' => 'en', 'text' => 'State']])
        ->and($data->name)
        ->toBe([['language' => 'en', 'text' => 'California']])
        ->and($data->officialLanguages)
        ->toBeNull()
        ->and($data->children)
        ->toBeNull();
});

test('getLocalizedName returns name in specified language', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'DE-BY',
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'category' => [
            ['language' => 'de', 'text' => 'Land'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'Bavaria'],
            ['language' => 'de', 'text' => 'Bayern'],
            ['language' => 'es', 'text' => 'Baviera'],
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
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
            ['language' => 'es', 'text' => 'California'],
        ],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('California');
});

test('getLocalizedName falls back to first available language when english not found', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'DE-BY',
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'category' => [
            ['language' => 'de', 'text' => 'Land'],
        ],
        'name' => [
            ['language' => 'de', 'text' => 'Bayern'],
            ['language' => 'es', 'text' => 'Baviera'],
        ],
    ]);

    expect($data->getLocalizedName('fr'))
        ->toBe('Bayern');
});

test('getLocalizedName falls back to shortName when no names available', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [],
    ]);

    expect($data->getLocalizedName('en'))
        ->toBe('CA');
});

test('getLocalizedName uses default language from config when none specified', function (): void {
    config()->set('openholidays.default_language', 'de');

    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'DE-BY',
        'isoCode' => 'DE-BY',
        'shortName' => 'BY',
        'category' => [
            ['language' => 'de', 'text' => 'Land'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'Bavaria'],
            ['language' => 'de', 'text' => 'Bayern'],
        ],
    ]);

    expect($data->getLocalizedName())
        ->toBe('Bayern');
});

test('toArray returns correct structure with all fields', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
        ],
        'officialLanguages' => ['en'],
        'children' => [
            [
                'code' => 'US-CA-LA',
                'isoCode' => 'US-CA-LA',
                'shortName' => 'LA',
                'category' => [['language' => 'en', 'text' => 'County']],
                'name' => [['language' => 'en', 'text' => 'Los Angeles']],
            ],
        ],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['code', 'isoCode', 'shortName', 'category', 'name', 'officialLanguages', 'children'])
        ->and($array['code'])
        ->toBe('US-CA')
        ->and($array['isoCode'])
        ->toBe('US-CA')
        ->and($array['shortName'])
        ->toBe('CA')
        ->and($array['category'])
        ->toBe([['language' => 'en', 'text' => 'State']])
        ->and($array['name'])
        ->toBe([['language' => 'en', 'text' => 'California']])
        ->and($array['officialLanguages'])
        ->toBe(['en'])
        ->and($array['children'])
        ->toBeArray()
        ->toHaveCount(1);
});

test('toArray returns correct structure with required fields only', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
        ],
    ]);

    $array = $data->toArray();

    expect($array)
        ->toHaveKeys(['code', 'isoCode', 'shortName', 'category', 'name'])
        ->and($array['code'])
        ->toBe('US-CA')
        ->and($array['isoCode'])
        ->toBe('US-CA')
        ->and($array['shortName'])
        ->toBe('CA')
        ->and($array['category'])
        ->toBe([['language' => 'en', 'text' => 'State']])
        ->and($array['name'])
        ->toBe([['language' => 'en', 'text' => 'California']]);
});

test('handles nested children structure', function (): void {
    $data = OpenHolidaysSubdivisionData::from([
        'code' => 'US-CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'category' => [
            ['language' => 'en', 'text' => 'State'],
        ],
        'name' => [
            ['language' => 'en', 'text' => 'California'],
        ],
        'children' => [
            [
                'code' => 'US-CA-LA',
                'isoCode' => 'US-CA-LA',
                'shortName' => 'LA',
                'category' => [['language' => 'en', 'text' => 'County']],
                'name' => [['language' => 'en', 'text' => 'Los Angeles']],
                'children' => [
                    [
                        'code' => 'US-CA-LA-DOWNTOWN',
                        'isoCode' => 'US-CA-LA-DOWNTOWN',
                        'shortName' => 'DOWNTOWN',
                        'category' => [['language' => 'en', 'text' => 'District']],
                        'name' => [['language' => 'en', 'text' => 'Downtown']],
                    ],
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
