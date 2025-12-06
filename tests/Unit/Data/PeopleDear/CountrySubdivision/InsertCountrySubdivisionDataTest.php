<?php

declare(strict_types=1);

use App\Data\PeopleDear\CountrySubdivision\InsertCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;
use Illuminate\Support\Str;

test('transforms country subdivision data correctly',
    /**
     * @throws Throwable
     */
    function (): void {

        $subdivisionData = [
            'countryId' => $countryId = Str::uuid7()->toString(),
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

        $data = InsertCountrySubdivisionData::from($subdivisionData);

        $transformed = $data->toArray();

        expect($transformed)
            ->toBeArray()
            ->and($transformed['country_id'])
            ->toBe($countryId)
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

test('creates country subdivision data instance correctly',
    /**
     * @throws Throwable
     */
    function (): void {

        $subdivisionData = [
            'countryId' => $countryId = Str::uuid7()->toString(),
            'countrySubdivisionId' => $subdivisionId = Str::uuid7()->toString(),
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

        $data = InsertCountrySubdivisionData::from($subdivisionData);

        expect($data)
            ->toBeInstanceOf(InsertCountrySubdivisionData::class)
            ->and($data->countryId)
            ->toBe($countryId)
            ->and($data->countrySubdivisionId)
            ->toBe($subdivisionId)
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

test('handles different subdivision types correctly',
    /**
     * @throws Throwable
     */
    function (): void {

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

        $data = InsertCountrySubdivisionData::from($subdivisionData);

        expect($data->type)
            ->toBe(CountrySubdivisionType::Province)
            ->toBeInstanceOf(CountrySubdivisionType::class);

    });

test('handles null parent subdivision correctly', function (): void {

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

    $data = InsertCountrySubdivisionData::from($subdivisionData);

    expect($data->countrySubdivisionId)->toBeNull();

});
