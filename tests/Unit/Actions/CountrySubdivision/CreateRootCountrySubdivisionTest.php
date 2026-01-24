<?php

declare(strict_types=1);

use App\Actions\CountrySubdivision\CreateRootCountrySubdivision;
use App\Data\PeopleDear\CountrySubdivision\CreateCountrySubdivisionData;
use App\Enums\CountrySubdivisionType;
use App\Models\Country;
use App\Models\CountrySubdivision;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        $this->country = Country::factory()->create();

        $this->action = resolve(CreateRootCountrySubdivision::class);
    });

test('creates root subdivision without children',
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

        $root = $this->action->handle($data);

        expect($root)
            ->toBeInstanceOf(CountrySubdivision::class)
            ->and($root->country_subdivision_id)
            ->toBeNull()
            ->and($root->iso_code)
            ->toBe('US-CA')
            ->and(CountrySubdivision::query()->count())
            ->toBe(1);
    });

test('creates root with direct children',
    /**
     * @throws Throwable
     */
    function (): void {
        $children = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles County'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN']
            ),
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'San Francisco County'],
                code: 'SF',
                isoCode: 'US-CA-SF',
                shortName: 'SF',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN']
            ),
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: $children
        );

        $root = $this->action->handle($data);

        expect(CountrySubdivision::query()->count())
            ->toBe(3)
            ->and($root->children)
            ->toHaveCount(2);

        /** @var CountrySubdivision $laCounty */
        $laCounty = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA')
            ->first();

        expect($laCounty->country_subdivision_id)
            ->toBe($root->id);
    });

test('creates root with nested children',
    /**
     * @throws Throwable
     */
    function (): void {
        $grandchildren = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Downtown LA'],
                code: 'DTLA',
                isoCode: 'US-CA-LA-DTLA',
                shortName: 'DTLA',
                type: CountrySubdivisionType::District,
                officialLanguages: ['EN']
            ),
        ]);

        $children = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles County'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN'],
                children: $grandchildren
            ),
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: $children
        );

        $root = $this->action->handle($data);

        expect(CountrySubdivision::query()->count())
            ->toBe(3);

        /** @var CountrySubdivision $laCounty */
        $laCounty = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA')
            ->first();

        /** @var CountrySubdivision $downtown */
        $downtown = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA-DTLA')
            ->first();

        expect($laCounty->country_subdivision_id)
            ->toBe($root->id)
            ->and($downtown->country_subdivision_id)
            ->toBe($laCounty->id);
    });

test('returns root subdivision',
    /**
     * @throws Throwable
     */
    function (): void {

        $children = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles County'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN']
            ),
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: $children
        );

        $root = $this->action->handle($data);

        expect($root->iso_code)
            ->toBe('US-CA')
            ->and($root->type)
            ->toBe(CountrySubdivisionType::State)
            ->and($root->country_subdivision_id)
            ->toBeNull();
    });

test('handles duplicate iso codes with upsert behavior',
    /**
     * @throws Throwable
     */
    function (): void {

        $children = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles County'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN']
            ),
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles Updated'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN']
            ),
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: $children
        );

        $root = $this->action->handle($data);

        expect(CountrySubdivision::query()->count())
            ->toBe(2)
            ->and($root->iso_code)
            ->toBe('US-CA');

        /** @var CountrySubdivision $child */
        $child = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA')
            ->first();

        expect($child->name)
            ->toBe(['EN' => 'Los Angeles Updated']);

    });

test('handles three levels of nesting',
    /**
     * @throws Throwable
     */
    function (): void {

        $greatGrandchildren = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Neighborhood 1'],
                code: 'N1',
                isoCode: 'US-CA-LA-DTLA-N1',
                shortName: 'N1',
                type: CountrySubdivisionType::Ward,
                officialLanguages: ['EN']
            ),
        ]);

        $grandchildren = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Downtown LA'],
                code: 'DTLA',
                isoCode: 'US-CA-LA-DTLA',
                shortName: 'DTLA',
                type: CountrySubdivisionType::District,
                officialLanguages: ['EN'],
                children: $greatGrandchildren
            ),
        ]);

        $children = collect([
            new CreateCountrySubdivisionData(
                countryId: $this->country->id,
                countrySubdivisionId: null,
                name: ['EN' => 'Los Angeles County'],
                code: 'LA',
                isoCode: 'US-CA-LA',
                shortName: 'LA',
                type: CountrySubdivisionType::County,
                officialLanguages: ['EN'],
                children: $grandchildren
            ),
        ]);

        $data = new CreateCountrySubdivisionData(
            countryId: $this->country->id,
            countrySubdivisionId: null,
            name: ['EN' => 'California'],
            code: 'CA',
            isoCode: 'US-CA',
            shortName: 'CA',
            type: CountrySubdivisionType::State,
            officialLanguages: ['EN'],
            children: $children
        );

        $root = $this->action->handle($data);

        expect(CountrySubdivision::query()->count())->toBe(4);

        /** @var CountrySubdivision $neighborhood */
        $neighborhood = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA-DTLA-N1')
            ->first();

        /** @var CountrySubdivision $district */
        $district = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA-DTLA')
            ->first();

        /** @var CountrySubdivision $county */
        $county = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA')
            ->first();

        expect($county->country_subdivision_id)
            ->toBe($root->id)
            ->and($district->country_subdivision_id)
            ->toBe($county->id)
            ->and($neighborhood->country_subdivision_id)
            ->toBe($district->id);
    });
