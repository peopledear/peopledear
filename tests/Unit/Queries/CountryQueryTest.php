<?php

declare(strict_types=1);

use App\Models\Country;
use App\Queries\CountryQuery;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function (): void {
    $this->query = new CountryQuery;
});

test('returns eloquent builder instance', function (): void {
    $builder = $this->query->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

test('builder returns country query builder', function (): void {
    $builder = $this->query->builder();

    expect($builder->getModel())->toBeInstanceOf(Country::class);
});

test('can retrieve all countries using builder', function (): void {
    /** @var Country $country1 */
    $country1 = Country::factory()->createQuietly();

    /** @var Country $country2 */
    $country2 = Country::factory()->createQuietly();

    $countries = $this->query->builder()->get();

    expect($countries)
        ->toHaveCount(2)
        ->first()->id->toBe($country1->id)
        ->and($countries->last())->id->toBe($country2->id);
});

test('can order countries using builder', function (): void {
    /** @var Country $country1 */
    $country1 = Country::factory()->createQuietly(['iso_code' => 'ZZ']);

    /** @var Country $country2 */
    $country2 = Country::factory()->createQuietly(['iso_code' => 'AA']);

    /** @var Country $country3 */
    $country3 = Country::factory()->createQuietly(['iso_code' => 'MM']);

    $countries = $this->query->builder()
        ->orderBy('iso_code')
        ->get();

    expect($countries)
        ->toHaveCount(3)
        ->first()->iso_code->toBe('AA')
        ->and($countries->last())->iso_code->toBe('ZZ');
});

test('returns empty collection when no countries exist', function (): void {
    $countries = $this->query->builder()->get();

    expect($countries)
        ->toBeEmpty()
        ->toHaveCount(0);
});
