<?php

declare(strict_types=1);

use App\Data\Integrations\OpenHolidays\OpenHolidaysSubdivisionData;
use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;
use App\Http\Integrations\OpenHolidays\Adapters\OpenHolidaysSubdivisionAdapter;

beforeEach(function (): void {
    $fixture = file_get_contents(base_path('tests/Fixtures/Saloon/OpenHolidays/portugal-subdivisions.json'));
    $data = json_decode($fixture, true);
    $this->subdivisions = collect(json_decode((string) $data['data'], true));
});

test('transforms simple subdivision correctly', function (): void {
    /** @var array<string, mixed> $firstMunicipality */
    $firstMunicipality = $this->subdivisions
        ->where('code', 'PT-AV')
        ->first()['children'][0];

    $openHolidaysData = OpenHolidaysSubdivisionData::from($firstMunicipality);

    /** @var OpenHolidaysSubdivisionAdapter $adapter */
    $adapter = app(OpenHolidaysSubdivisionAdapter::class);

    $createData = $adapter->toCreateData(
        $openHolidaysData,
        countryId: 1,
        countryLanguages: ['pt']
    );

    expect($createData)
        ->toBeInstanceOf(CreateCountrySubdivisionData::class)
        ->and($createData->countryId)
        ->toBe(1)
        ->and($createData->code)
        ->toBe('PT-AV-AG')
        ->and($createData->isoCode)
        ->toBe('PT-AV-AG')
        ->and($createData->shortName)
        ->toBe('AV-AG')
        ->and($createData->type)
        ->toBe(CountrySubdivisionType::Municipality)
        ->and($createData->officialLanguages)
        ->toBe(['PT']);
});

test('transforms nested children recursively', function (): void {
    /** @var array<string, mixed> $aveiro */
    $aveiro = $this->subdivisions
        ->where('code', 'PT-AV')
        ->first();

    $openHolidaysData = OpenHolidaysSubdivisionData::from($aveiro);

    /** @var OpenHolidaysSubdivisionAdapter $adapter */
    $adapter = app(OpenHolidaysSubdivisionAdapter::class);

    $createData = $adapter->toCreateData(
        $openHolidaysData,
        countryId: 1,
        countryLanguages: ['pt']
    );

    expect($createData->children)
        ->not->toBeNull()
        ->toHaveCount(19)
        ->each
        ->toBeInstanceOf(CreateCountrySubdivisionData::class);
});

test('parses comma-separated official languages to array', function (): void {
    /** @var array<string, mixed> $subdivisionWithMultipleLanguages */
    $subdivisionWithMultipleLanguages = [
        'code' => 'ES-MD',
        'isoCode' => 'ES-M',
        'shortName' => 'MD',
        'category' => [['language' => 'ES', 'text' => 'Comunidad de Madrid']],
        'name' => [['language' => 'ES', 'text' => 'Madrid']],
        'officialLanguages' => ['ES', 'CA'],
    ];

    $openHolidaysData = OpenHolidaysSubdivisionData::from($subdivisionWithMultipleLanguages);

    /** @var OpenHolidaysSubdivisionAdapter $adapter */
    $adapter = app(OpenHolidaysSubdivisionAdapter::class);

    $createData = $adapter->toCreateData(
        $openHolidaysData,
        countryId: 2,
        countryLanguages: ['es']
    );

    expect($createData->officialLanguages)
        ->toBe(['ES', 'CA']);
});

test('inherits country languages when subdivision languages empty', function (): void {
    /** @var array<string, mixed> $subdivisionWithoutLanguages */
    $subdivisionWithoutLanguages = [
        'code' => 'PT-LI',
        'isoCode' => 'PT-10',
        'shortName' => 'LI',
        'category' => [['language' => 'PT', 'text' => 'distrito']],
        'name' => [['language' => 'PT', 'text' => 'Lisboa']],
        'officialLanguages' => null,
    ];

    $openHolidaysData = OpenHolidaysSubdivisionData::from($subdivisionWithoutLanguages);

    /** @var OpenHolidaysSubdivisionAdapter $adapter */
    $adapter = app(OpenHolidaysSubdivisionAdapter::class);

    $createData = $adapter->toCreateData(
        $openHolidaysData,
        countryId: 1,
        countryLanguages: ['pt', 'en']
    );

    expect($createData->officialLanguages)
        ->toBe(['pt', 'en']);
});

test('preserves full ISO code in both code and isoCode fields', function (): void {
    /** @var array<string, mixed> $aveiro */
    $aveiro = $this->subdivisions
        ->where('code', 'PT-AV')
        ->first();

    $openHolidaysData = OpenHolidaysSubdivisionData::from($aveiro);

    /** @var OpenHolidaysSubdivisionAdapter $adapter */
    $adapter = app(OpenHolidaysSubdivisionAdapter::class);

    $createData = $adapter->toCreateData(
        $openHolidaysData,
        countryId: 1,
        countryLanguages: ['pt']
    );

    expect($createData->code)
        ->toBe('PT-AV')
        ->and($createData->isoCode)
        ->toBe('PT-AV');
});
