<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;

test('country can be created with factory', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    expect($country)
        ->toBeInstanceOf(Country::class)
        ->and($country->id)
        ->toBeString()
        ->and($country->iso_code)
        ->toBeString()
        ->and($country->name)
        ->toBeArray()
        ->and($country->official_languages)
        ->toBeArray();
});

test('country iso_code is unique', function (): void {

    Country::factory()->create([
        'iso_code' => 'XX',
    ]);

    expect(fn () => Country::factory()->create([
        'iso_code' => 'XX',
    ]))
        ->toThrow(QueryException::class);
});

test('country name is cast to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'name' => [
            'EN' => 'Test Country',
            'DE' => 'Testland',
            'PT' => 'País de Teste',
        ],
    ]);

    expect($country->name)
        ->toBeArray()
        ->and($country->name['EN'])
        ->toBe('Test Country')
        ->and($country->name['DE'])
        ->toBe('Testland')
        ->and($country->name['PT'])
        ->toBe('País de Teste');
});

test('country official_languages is cast to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'official_languages' => ['EN', 'FR', 'DE'],
    ]);

    expect($country->official_languages)
        ->toBeArray()
        ->and($country->official_languages)
        ->toBe(['EN', 'FR', 'DE'])
        ->and($country->official_languages)
        ->toHaveCount(3);
});

test('country can have single official language', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'official_languages' => ['PT'],
    ]);

    expect($country->official_languages)
        ->toBeArray()
        ->and($country->official_languages)
        ->toHaveCount(1)
        ->and($country->official_languages[0])
        ->toBe('PT');
});

test('country can have multiple official languages', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'official_languages' => ['DE', 'FR', 'IT', 'RM'],
    ]);

    expect($country->official_languages)
        ->toBeArray()
        ->and($country->official_languages)
        ->toHaveCount(4)
        ->and($country->official_languages)
        ->toContain('DE', 'FR', 'IT', 'RM');
});

test('country has subdivisions relationship', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    expect($country->subdivisions())->toBeInstanceOf(HasMany::class);
});

test('country subdivisions relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'US',
        'name' => ['EN' => 'United States'],
        'official_languages' => ['EN'],
    ]);

    /** @var CountrySubdivision $subdivision1 */
    $subdivision1 = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'name' => ['EN' => 'California'],
    ]);

    /** @var CountrySubdivision $subdivision2 */
    $subdivision2 = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'name' => ['EN' => 'Texas'],
    ]);

    $country->load('subdivisions');

    expect($country->subdivisions)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($country->subdivisions->pluck('name')->map(fn ($name): mixed => $name['EN'])->toArray())
        ->toBe(['California', 'Texas']);
});

test('to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()
        ->create()
        ->refresh();

    expect(array_keys($country->toArray()))
        ->toBe([
            'id',
            'iso_code',
            'name',
            'official_languages',
        ]);
});
