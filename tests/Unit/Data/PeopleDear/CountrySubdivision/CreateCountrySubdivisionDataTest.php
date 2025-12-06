<?php

declare(strict_types=1);

use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;
use Illuminate\Support\Str;

test('does not transform arrays to JSON strings', function (): void {

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

    $data = CreateCountrySubdivisionData::from($subdivisionData);

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
        ->toBeArray()
        ->toBe(['EN' => 'California', 'ES' => 'California'])
        ->and($transformed['official_languages'])
        ->toBeArray()
        ->toBe(['EN', 'ES']);

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

        $data = CreateCountrySubdivisionData::from($subdivisionData);

        expect($data)
            ->toBeInstanceOf(CreateCountrySubdivisionData::class)
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

        $data = CreateCountrySubdivisionData::from($subdivisionData);

        expect($data->type)
            ->toBe(CountrySubdivisionType::Province)
            ->toBeInstanceOf(CountrySubdivisionType::class);

    });

test('handles null parent subdivision correctly',
    /**
     * @throws Throwable
     */
    function (): void {

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

test('handles children collection correctly',
    /**
     * @throws Throwable
     */
    function (): void {

        $childData = [
            'countryId' => 1,
            'countrySubdivisionId' => null,
            'name' => ['EN' => 'Los Angeles County'],
            'code' => 'LA',
            'isoCode' => 'US-CA-LA',
            'shortName' => 'LA',
            'type' => CountrySubdivisionType::County,
            'officialLanguages' => ['EN'],
        ];

        $subdivisionData = [
            'countryId' => 1,
            'countrySubdivisionId' => null,
            'name' => ['EN' => 'California'],
            'code' => 'CA',
            'isoCode' => 'US-CA',
            'shortName' => 'CA',
            'type' => CountrySubdivisionType::State,
            'officialLanguages' => ['EN'],
            'children' => collect([CreateCountrySubdivisionData::from($childData)]),
        ];

        $data = CreateCountrySubdivisionData::from($subdivisionData);

        expect($data->children)
            ->not->toBeNull()
            ->toHaveCount(1)
            ->and($data->children->first())
            ->toBeInstanceOf(CreateCountrySubdivisionData::class);

    });
