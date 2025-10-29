<?php

declare(strict_types=1);

use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;

test('it transforms country subdivision data correctly', function (): void {

    $subdivisionData = [
        'countryId' => 1,
        'countrySubdivisionId' => null,
        'name' => [
            'EN' => 'California',
            'ES' => 'California',
        ],
        'code' => 'CA',
        'isoCode' => 'US-CA',
        'shortName' => 'CA',
        'type' => CountrySubdivisionType::State,
        'officialLanguages' => ['EN', 'ES'],
    ];

    $data = CreateCountrySubdivisionData::from($subdivisionData);

    $transformed = $data->toArray();

    expect($transformed)
        ->toBeArray()
        ->and($transformed['country_id'])
        ->toBe(1)
        ->and($transformed['country_subdivision_id'])
        ->toBeNull()
        ->and($transformed['code'])
        ->toBe('CA')
        ->and($transformed['iso_code'])
        ->toBe('US-CA')
        ->and($transformed['short_name'])
        ->toBe('CA')
        ->and($transformed['type'])
        ->toBe(CountrySubdivisionType::State->value)
        ->and($transformed['name'])
        ->toBe(json_encode(['EN' => 'California', 'ES' => 'California']))
        ->and($transformed['official_languages'])
        ->toBe(json_encode(['EN', 'ES']));

});

test('it creates country subdivision data instance correctly', function (): void {

    $subdivisionData = [
        'countryId' => 2,
        'countrySubdivisionId' => 5,
        'name' => [
            'EN' => 'Bavaria',
            'DE' => 'Bayern',
        ],
        'code' => 'BY',
        'isoCode' => 'DE-BY',
        'shortName' => 'Bayern',
        'type' => CountrySubdivisionType::State,
        'officialLanguages' => ['DE'],
    ];

    $data = CreateCountrySubdivisionData::from($subdivisionData);

    expect($data)
        ->toBeInstanceOf(CreateCountrySubdivisionData::class)
        ->and($data->countryId)
        ->toBe(2)
        ->and($data->countrySubdivisionId)
        ->toBe(5)
        ->and($data->code)
        ->toBe('BY')
        ->and($data->isoCode)
        ->toBe('DE-BY')
        ->and($data->shortName)
        ->toBe('Bayern')
        ->and($data->type)
        ->toBe(CountrySubdivisionType::State)
        ->and($data->name)
        ->toBeArray()
        ->and($data->name['EN'])
        ->toBe('Bavaria')
        ->and($data->name['DE'])
        ->toBe('Bayern')
        ->and($data->officialLanguages)
        ->toBeArray()
        ->and($data->officialLanguages)
        ->toContain('DE');

});

test('it handles different subdivision types correctly', function (): void {

    $subdivisionData = [
        'countryId' => 3,
        'countrySubdivisionId' => null,
        'name' => ['EN' => 'Ontario'],
        'code' => 'ON',
        'isoCode' => 'CA-ON',
        'shortName' => 'ON',
        'type' => CountrySubdivisionType::Province,
        'officialLanguages' => ['EN', 'FR'],
    ];

    $data = CreateCountrySubdivisionData::from($subdivisionData);

    expect($data->type)
        ->toBe(CountrySubdivisionType::Province)
        ->toBeInstanceOf(CountrySubdivisionType::class);

});

test('it handles null parent subdivision correctly', function (): void {

    $subdivisionData = [
        'countryId' => 1,
        'countrySubdivisionId' => null,
        'name' => ['EN' => 'Texas'],
        'code' => 'TX',
        'isoCode' => 'US-TX',
        'shortName' => 'TX',
        'type' => CountrySubdivisionType::State,
        'officialLanguages' => ['EN'],
    ];

    $data = CreateCountrySubdivisionData::from($subdivisionData);

    expect($data->countrySubdivisionId)->toBeNull();

});
