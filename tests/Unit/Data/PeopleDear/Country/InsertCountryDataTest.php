<?php

declare(strict_types=1);

use App\Data\PeopleDear\Country\InsertCountryData;

beforeEach(function (): void {

    $contents = file_get_contents(database_path('data/countries.json'));
    $data = json_decode($contents, true);
    $this->countries = collect($data);

});

test('it transforms country data correctly', function (): void {

    $germany = $this->countries->firstWhere('isoCode', 'DE');

    $countryData = InsertCountryData::from($germany);

    $transformed = $countryData->toArray();

    expect($transformed)
        ->toBeArray()
        ->and($transformed['iso_code'])
        ->toBe('DE')
        ->and($transformed['name'])
        ->toBe(json_encode(['EN' => 'Germany', 'DE' => 'Deutschland']))
        ->and($transformed['official_languages'])
        ->toBe(json_encode(['DE']));

});

test('it creates country data instance correctly', function (): void {

    $portugal = $this->countries->firstWhere('isoCode', 'PT');

    $countryData = InsertCountryData::from($portugal);

    expect($countryData)
        ->toBeInstanceOf(InsertCountryData::class)
        ->and($countryData->isoCode)
        ->toBe('PT')
        ->and($countryData->name)
        ->toBeArray()
        ->and($countryData->name['EN'])
        ->toBe('Portugal')
        ->and($countryData->officialLanguages)
        ->toBeArray()
        ->and($countryData->officialLanguages)
        ->toContain('PT');

});
