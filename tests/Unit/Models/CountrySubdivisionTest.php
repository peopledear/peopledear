<?php

declare(strict_types=1);

use App\Enums\PeopleDear\CountrySubdivisionType;
use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('country subdivision can be created with factory', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
    ]);

    expect($subdivision)
        ->toBeInstanceOf(CountrySubdivision::class)
        ->and($subdivision->id)
        ->toBeString()
        ->and($subdivision->country_id)
        ->toBeString()
        ->and($subdivision->country_subdivision_id)
        ->toBeNull()
        ->and($subdivision->code)
        ->toBeString()
        ->and($subdivision->iso_code)
        ->toBeString()
        ->and($subdivision->short_name)
        ->toBeString()
        ->and($subdivision->name)
        ->toBeArray()
        ->and($subdivision->official_languages)
        ->toBeArray();
});

test('country subdivision type is cast to enum', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'type' => CountrySubdivisionType::State->value,
    ]);

    expect($subdivision->type)
        ->toBeInstanceOf(CountrySubdivisionType::class)
        ->and($subdivision->type)
        ->toBe(CountrySubdivisionType::State);
});

test('country subdivision name is cast to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'name' => [
            'EN' => 'California',
            'ES' => 'California',
            'PT' => 'Califórnia',
        ],
    ]);

    expect($subdivision->name)
        ->toBeArray()
        ->and($subdivision->name['EN'])
        ->toBe('California')
        ->and($subdivision->name['ES'])
        ->toBe('California')
        ->and($subdivision->name['PT'])
        ->toBe('Califórnia');
});

test('country subdivision official_languages is cast to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'official_languages' => ['EN', 'ES'],
    ]);

    expect($subdivision->official_languages)
        ->toBeArray()
        ->and($subdivision->official_languages)
        ->toBe(['EN', 'ES'])
        ->and($subdivision->official_languages)
        ->toHaveCount(2);
});

test('country subdivision has country relationship', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
    ]);

    expect($subdivision->country())->toBeInstanceOf(BelongsTo::class);
});

test('country subdivision country relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'US',
        'name' => ['EN' => 'United States'],
        'official_languages' => ['EN'],
    ]);

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
    ]);

    $subdivision->load('country');

    expect($subdivision->country)
        ->toBeInstanceOf(Country::class)
        ->and($subdivision->country->id)
        ->toBe($country->id)
        ->and($subdivision->country->iso_code)
        ->toBe('US');
});

test('country subdivision has parent relationship', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
    ]);

    expect($subdivision->parent())->toBeInstanceOf(BelongsTo::class);
});

test('country subdivision parent relationship can be null', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => null,
    ]);

    expect($subdivision->country_subdivision_id)
        ->toBeNull()
        ->and($subdivision->parent)
        ->toBeNull();
});

test('country subdivision parent relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $parent */
    $parent = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'name' => ['EN' => 'California'],
        'type' => CountrySubdivisionType::State->value,
    ]);

    /** @var CountrySubdivision $child */
    $child = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => $parent->id,
        'name' => ['EN' => 'Los Angeles County'],
        'type' => CountrySubdivisionType::County->value,
    ]);

    $child->load('parent');

    expect($child->parent)
        ->toBeInstanceOf(CountrySubdivision::class)
        ->and($child->parent->id)
        ->toBe($parent->id)
        ->and($child->parent->name['EN'])
        ->toBe('California');
});

test('country subdivision has children relationship', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
    ]);

    expect($subdivision->children())->toBeInstanceOf(HasMany::class);
});

test('country subdivision children relationship is properly loaded', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $parent */
    $parent = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'name' => ['EN' => 'California'],
        'type' => CountrySubdivisionType::State->value,
    ]);

    /** @var CountrySubdivision $child1 */
    $child1 = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => $parent->id,
        'name' => ['EN' => 'Los Angeles County'],
    ]);

    /** @var CountrySubdivision $child2 */
    $child2 = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => $parent->id,
        'name' => ['EN' => 'San Francisco County'],
    ]);

    $parent->load('children');

    expect($parent->children)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($parent->children->pluck('name')->map(fn ($name): mixed => $name['EN'])->toArray())
        ->toBe(['Los Angeles County', 'San Francisco County']);
});

test('country subdivision hierarchical structure works', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create([
        'iso_code' => 'US',
        'name' => ['EN' => 'United States'],
    ]);

    /** @var CountrySubdivision $state */
    $state = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => null,
        'name' => ['EN' => 'California'],
        'type' => CountrySubdivisionType::State->value,
    ]);

    /** @var CountrySubdivision $county */
    $county = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => $state->id,
        'name' => ['EN' => 'Los Angeles County'],
        'type' => CountrySubdivisionType::County->value,
    ]);

    /** @var CountrySubdivision $city */
    $city = CountrySubdivision::factory()->create([
        'country_id' => $country->id,
        'country_subdivision_id' => $county->id,
        'name' => ['EN' => 'Los Angeles'],
        'type' => CountrySubdivisionType::City->value,
    ]);

    $city->load('parent.parent.country');

    expect($city->parent)
        ->toBeInstanceOf(CountrySubdivision::class)
        ->and($city->parent->id)
        ->toBe($county->id)
        ->and($city->parent->parent)
        ->toBeInstanceOf(CountrySubdivision::class)
        ->and($city->parent->parent->id)
        ->toBe($state->id)
        ->and($city->parent->parent->parent)
        ->toBeNull()
        ->and($city->parent->parent->country)
        ->toBeInstanceOf(Country::class)
        ->and($city->parent->parent->country->iso_code)
        ->toBe('US');
});

test('to array', function (): void {
    /** @var Country $country */
    $country = Country::factory()->create();

    /** @var CountrySubdivision $subdivision */
    $subdivision = CountrySubdivision::factory()
        ->create([
            'country_id' => $country->id,
        ])
        ->refresh();

    expect(array_keys($subdivision->toArray()))
        ->toBe([
            'id',
            'country_id',
            'country_subdivision_id',
            'name',
            'code',
            'iso_code',
            'short_name',
            'type',
            'official_languages',
        ]);
});
