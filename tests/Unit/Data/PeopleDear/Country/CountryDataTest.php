<?php

declare(strict_types=1);

use App\Data\PeopleDear\Country\CountryData;
use App\Models\Country;
use Illuminate\Support\Str;

test('creates country data from country model', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTUS',
        'name' => ['EN' => 'United States', 'FR' => '?tats-Unis'],
    ]);

    $data = CountryData::from($country);

    expect($data)
        ->toBeInstanceOf(CountryData::class)
        ->id->toBe($country->id)
        ->isoCode->toBe('TESTUS')
        ->name->toBe(['EN' => 'United States', 'FR' => '?tats-Unis']);
});

test('displays name from en key when available', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTEN',
        'name' => ['en' => 'United States', 'FR' => '?tats-Unis'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('United States');
});

test('displays name from EN key when en key not available', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTEN2',
        'name' => ['EN' => 'United States', 'FR' => '?tats-Unis'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('United States');
});

test('displays name from first key when en and EN not available', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTFR',
        'name' => ['FR' => '?tats-Unis', 'DE' => 'Vereinigte Staaten'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('?tats-Unis');
});

test('displays iso code when name array is empty', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTEMPTY',
        'name' => [],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('TESTEMPTY');
});

test('toArray includes computed displayName property', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTCOMP',
        'name' => ['EN' => 'United States', 'FR' => '?tats-Unis'],
    ]);

    $data = CountryData::from($country);
    $array = $data->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKeys(['id', 'isoCode', 'name', 'displayName'])
        ->and($array['id'])->toBe($country->id)
        ->and($array['isoCode'])->toBe('TESTCOMP')
        ->and($array['name'])->toBe(['EN' => 'United States', 'FR' => '?tats-Unis'])
        ->and($array['displayName'])->toBe('United States');
});

test('toArray outputs properties in camelCase', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTCASE',
        'name' => ['EN' => 'United Kingdom'],
    ]);

    $data = CountryData::from($country);
    $array = $data->toArray();

    expect($array)
        ->toHaveKey('isoCode')  // ? CamelCase, not iso_code
        ->not->toHaveKey('iso_code')
        ->toHaveKey('displayName')  // ? CamelCase, not display_name
        ->not->toHaveKey('display_name');
});

test('computed displayName property is accessible', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTPROP',
        'name' => ['EN' => 'United States', 'FR' => '?tats-Unis'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('United States');
});

test('can create country data from array with camel Case keys', function (): void {
    $data = CountryData::from([
        'id' => $id = Str::uuid7()->toString(),
        'isoCode' => 'CA',  // Use property name directly for array input
        'name' => ['EN' => 'Canada'],
    ]);

    expect($data)
        ->toBeInstanceOf(CountryData::class)
        ->id->toBe($id)
        ->isoCode->toBe('CA')
        ->name->toBe(['EN' => 'Canada'])
        ->displayName->toBe('Canada');
});

test('computed displayName is calculated in constructor', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTDYN',
        'name' => ['DE' => 'Deutschland', 'EN' => 'Germany'],
    ]);

    $data = CountryData::from($country);

    // Computed property is set in constructor
    expect($data->displayName)->toBe('Germany');
});

test('displayName prioritizes en over EN over first key over isoCode', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTPRIO',
        'name' => ['en' => 'English Name', 'EN' => 'Uppercase Name', 'FR' => 'French Name'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('English Name');
});

test('displayName uses EN when en not available', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTEN3',
        'name' => ['EN' => 'Uppercase Name', 'FR' => 'French Name'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('Uppercase Name');
});

test('displayName uses first key when en and EN not available', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTFIRST',
        'name' => ['FR' => 'French Name', 'DE' => 'German Name'],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('French Name');
});

test('displayName falls back to isoCode when name array is empty', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'TESTFALLBACK',
        'name' => [],
    ]);

    $data = CountryData::from($country);

    expect($data->displayName)->toBe('TESTFALLBACK');
});
