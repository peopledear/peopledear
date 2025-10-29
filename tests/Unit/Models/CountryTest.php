<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;

test('country can be created with factory', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly();

    expect($country)
        ->toBeInstanceOf(Country::class)
        ->and($country->id)
        ->toBeInt()
        ->and($country->iso_code)
        ->toBeString()
        ->and($country->name)
        ->toBeArray()
        ->and($country->official_languages)
        ->toBeArray();
});

test('country iso_code is unique', function (): void {

    Country::factory()->createQuietly([
        'iso_code' => 'XX',
    ]);

    expect(fn () => Country::factory()->createQuietly([
        'iso_code' => 'XX',
    ]))
        ->toThrow(QueryException::class);
});

test('country name is cast to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly([
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
    $country = Country::factory()->createQuietly([
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
    $country = Country::factory()->createQuietly([
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
    $country = Country::factory()->createQuietly([
        'official_languages' => ['DE', 'FR', 'IT', 'RM'],
    ]);

    expect($country->official_languages)
        ->toBeArray()
        ->and($country->official_languages)
        ->toHaveCount(4)
        ->and($country->official_languages)
        ->toContain('DE', 'FR', 'IT', 'RM');
});

test('seeded country portugal exists', function (): void {
    /** @var Country|null $country */
    $country = Country::query()
        ->where('iso_code', 'PT')
        ->first()
        ?->fresh();

    expect($country)
        ->not->toBeNull()
        ->and($country->iso_code)
        ->toBe('PT')
        ->and($country->name['EN'])
        ->toBe('Portugal')
        ->and($country->name['PT'])
        ->toBe('Portugal')
        ->and($country->official_languages)
        ->toBe(['PT']);
});

test('seeded country switzerland has multiple languages', function (): void {
    /** @var Country|null $country */
    $country = Country::query()
        ->where('iso_code', 'CH')
        ->first()
        ?->fresh();

    expect($country)
        ->not->toBeNull()
        ->and($country->iso_code)
        ->toBe('CH')
        ->and($country->name['EN'])
        ->toBe('Switzerland')
        ->and($country->name['DE'])
        ->toBe('Schweiz')
        ->and($country->official_languages)
        ->toBe(['DE', 'FR', 'IT', 'RM'])
        ->and($country->official_languages)
        ->toHaveCount(4);
});

test('seeded country belgium has multiple name translations', function (): void {
    /** @var Country|null $country */
    $country = Country::query()
        ->where('iso_code', 'BE')
        ->first()
        ?->fresh();

    expect($country)
        ->not->toBeNull()
        ->and($country->name['EN'])
        ->toBe('Belgium')
        ->and($country->name['NL'])
        ->toBe('België')
        ->and($country->name['FR'])
        ->toBe('Belgique (la)')
        ->and($country->name['DE'])
        ->toBe('Belgien')
        ->and($country->official_languages)
        ->toBe(['NL', 'FR', 'DE']);
});

test('country has organizations relationship', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly();

    expect($country->organizations())->toBeInstanceOf(HasMany::class);
});

test('country organizations relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->createQuietly([
        'iso_code' => 'US',
        'name' => ['en' => 'United States'],
        'official_languages' => ['en'],
    ]);

    /** @var Organization $org1 */
    $org1 = Organization::factory()->createQuietly([
        'country_id' => $country->id,
        'name' => 'Company A',
    ]);

    /** @var Organization $org2 */
    $org2 = Organization::factory()->createQuietly([
        'country_id' => $country->id,
        'name' => 'Company B',
    ]);

    $country->load('organizations');

    expect($country->organizations)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($country->organizations->pluck('name')->toArray())
        ->toBe(['Company A', 'Company B']);
});

test('to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()
        ->createQuietly()
        ->refresh();

    expect(array_keys($country->toArray()))
        ->toBe([
            'id',
            'iso_code',
            'name',
            'official_languages',
        ]);
});
