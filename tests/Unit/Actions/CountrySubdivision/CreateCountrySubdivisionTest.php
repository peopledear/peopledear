<?php

declare(strict_types=1);

use App\Actions\CountrySubdivision\CreateCountrySubdivision;
use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;
use App\Models\Country;
use App\Models\CountrySubdivision;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        $this->country = Country::factory()->create();

        $this->action = app(CreateCountrySubdivision::class);
    });

test('upserts new subdivision when iso_code does not exist',
    /**
     * @throws Throwable
     */
    function (): void {
        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'Lisbon'],
            code: 'PT-11',
            isoCode: 'PT-11',
            shortName: 'Lisboa',
            type: CountrySubdivisionType::District,
            officialLanguages: ['PT']
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision)
            ->toBeInstanceOf(CountrySubdivision::class)
            ->and($subdivision->iso_code)
            ->toBe('PT-11')
            ->and($subdivision->name['EN'])
            ->toBe('Lisbon')
            ->and(CountrySubdivision::query()->where('iso_code', 'PT-11')->count())
            ->toBe(1);
    });

test('upserts existing subdivision when iso_code already exists',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var CountrySubdivision $existing */
        $existing = CountrySubdivision::factory()->create([
            'country_id' => $this->country->id,
            'iso_code' => 'PT-11',
            'name' => ['EN' => 'Old Name'],
            'short_name' => 'Old',
        ]);

        $existingId = $existing->id;

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'Lisbon', 'PT' => 'Lisboa'],
            code: 'PT-11',
            isoCode: 'PT-11',
            shortName: 'Lisboa',
            type: CountrySubdivisionType::District,
            officialLanguages: ['PT']
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision->id)
            ->toBe($existingId)
            ->and($subdivision->name)
            ->toBe(['EN' => 'Lisbon', 'PT' => 'Lisboa'])
            ->and($subdivision->short_name)
            ->toBe('Lisboa')
            ->and(CountrySubdivision::query()->where('iso_code', 'PT-11')->count())
            ->toBe(1);
    });

test('creates a single country subdivision',
    /**
     * @throws Throwable
     */
    function (): void {
        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN']
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision)
            ->toBeInstanceOf(CountrySubdivision::class)
            ->and($subdivision->id)
            ->toBeString()
            ->and($subdivision->country_id)
            ->toBe($this->country->id)
            ->and($subdivision->iso_code)
            ->toBe('US-CA')
            ->and($subdivision->name)
            ->toBeArray()
            ->and($subdivision->name['EN'])
            ->toBe('California');
    });

test('creates subdivision with parent',
    /**
     * @throws Throwable
     */
    function (): void {
        /** @var CountrySubdivision $parent */
        $parent = CountrySubdivision::factory()->create([
            'country_id' => $this->country->id,
            'iso_code' => 'US-CA',
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: $parent->id,
            name: ['EN' => 'Los Angeles County'],
            code: 'LA',
            isoCode: 'US-CA-LA',
            shortName: 'LA',
            type: CountrySubdivisionType::County,
            officialLanguages: ['EN']
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision->country_subdivision_id)
            ->toBe($parent->id)
            ->and($subdivision->parent->id)
            ->toBe($parent->id);
    });

test('ignores children property if provided',
    /**
     * @throws Throwable
     */
    function (): void {
        $childData = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'Los Angeles County'],
            code: 'LA',
            isoCode: 'US-CA-LA',
            shortName: 'LA',
            type: CountrySubdivisionType::County,
            officialLanguages: ['EN']
        );

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: collect([$childData])
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision)
            ->toBeInstanceOf(CountrySubdivision::class)
            ->and(CountrySubdivision::query()->count())
            ->toBe(1);
    });

test('returns persisted model with all attributes',
    /**
     * @throws Throwable
     */
    function (): void {
        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'Bavaria', 'DE' => 'Bayern'],
            code: 'BY',
            isoCode: 'DE-BY',
            shortName: 'Bayern',
            type: CountrySubdivisionType::Land,
            officialLanguages: ['DE']
        );

        $subdivision = $this->action->handle($data);

        expect($subdivision->exists)
            ->toBeTrue()
            ->and($subdivision->code)
            ->toBe('BY')
            ->and($subdivision->short_name)
            ->toBe('Bayern')
            ->and($subdivision->type)
            ->toBe(CountrySubdivisionType::Land)
            ->and($subdivision->name)
            ->toBe(['EN' => 'Bavaria', 'DE' => 'Bayern'])
            ->and($subdivision->official_languages)
            ->toBe(['DE']);
    });
