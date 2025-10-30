<?php

declare(strict_types=1);

use App\Actions\CountrySubdivision\UpsertCountrySubdivisions;
use App\Data\PeopleDear\CountrySubdivision\InsertCountrySubdivisionData;
use App\Enums\PeopleDear\CountrySubdivisionType;
use App\Models\Country;
use App\Models\CountrySubdivision;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {
        $this->usa = Country::factory()->createQuietly(['iso_code' => 'US']);
        $this->germany = Country::factory()->createQuietly(['iso_code' => 'DE']);

        $this->subdivisions = collect([
            [
                'countryId' => $this->usa->id,
                'countrySubdivisionId' => null,
                'name' => ['EN' => 'California', 'ES' => 'California'],
                'code' => 'CA',
                'isoCode' => 'US-CA',
                'shortName' => 'CA',
                'type' => CountrySubdivisionType::State,
                'officialLanguages' => ['EN'],
            ],
            [
                'countryId' => $this->usa->id,
                'countrySubdivisionId' => null,
                'name' => ['EN' => 'Texas', 'ES' => 'Texas'],
                'code' => 'TX',
                'isoCode' => 'US-TX',
                'shortName' => 'TX',
                'type' => CountrySubdivisionType::State,
                'officialLanguages' => ['EN'],
            ],
            [
                'countryId' => $this->germany->id,
                'countrySubdivisionId' => null,
                'name' => ['EN' => 'Bavaria', 'DE' => 'Bayern'],
                'code' => 'BY',
                'isoCode' => 'DE-BY',
                'shortName' => 'BY',
                'type' => CountrySubdivisionType::Land,
                'officialLanguages' => ['DE'],
            ],
        ]);

        /** @var UpsertCountrySubdivisions $this action */
        $this->action = app(UpsertCountrySubdivisions::class);
        $this->collectionOfInsertSubdivision = $this->subdivisions->map(fn (array $subdivision): InsertCountrySubdivisionData => InsertCountrySubdivisionData::from($subdivision));
    });

test('seeds all subdivisions from data',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(CountrySubdivision::query()->count())->toBe(0);

        $this->action->handle($this->collectionOfInsertSubdivision);

        expect(CountrySubdivision::query()->count())
            ->toBe($this->subdivisions->count());
    });

test('upserts subdivisions idempotently',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertSubdivision);

        $initialCount = CountrySubdivision::query()->count();

        expect($initialCount)->toBe($this->subdivisions->count());

        $this->action->handle($this->collectionOfInsertSubdivision);

        expect(CountrySubdivision::query()->count())->toBe($initialCount);
    });

test('updates existing subdivision data',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertSubdivision);

        CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->update(['name' => json_encode(['EN' => 'Modified'])]);

        /** @var CountrySubdivision $modified */
        $modified = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        expect($modified->name)->toBe(['EN' => 'Modified']);

        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $updated */
        $updated = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first()
            ?->fresh();

        expect($updated->name)
            ->not->toBe(['EN' => 'Modified'])
            ->and($updated->name['EN'])
            ->toBe('California');
    });

test('preserves subdivision id when upserting',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $california */
        $california = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        $originalId = $california->id;

        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $after */
        $after = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        expect($after->id)->toBe($originalId);
    });

test('seeds subdivisions with correct structure',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $california */
        $california = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        expect($california)
            ->not->toBeNull()
            ->and($california->iso_code)
            ->toBe('US-CA')
            ->and($california->code)
            ->toBe('CA')
            ->and($california->short_name)
            ->toBe('CA')
            ->and($california->type)
            ->toBe(CountrySubdivisionType::State)
            ->and($california->name)
            ->toBeArray()
            ->and($california->official_languages)
            ->toBeArray()
            ->and($california->official_languages)
            ->toBe(['EN']);
    });

test('seeds subdivisions with different types correctly',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $california */
        $california = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        /** @var CountrySubdivision $bavaria */
        $bavaria = CountrySubdivision::query()
            ->where('iso_code', 'DE-BY')
            ->first();

        expect($california->type)
            ->toBe(CountrySubdivisionType::State)
            ->and($bavaria->type)
            ->toBe(CountrySubdivisionType::Land);
    });

test('handles hierarchical subdivisions correctly',
    /**
     * @throws Throwable
     */
    function (): void {

        $this->action->handle($this->collectionOfInsertSubdivision);

        /** @var CountrySubdivision $california */
        $california = CountrySubdivision::query()
            ->where('iso_code', 'US-CA')
            ->first();

        $countyData = collect([
            [
                'countryId' => $this->usa->id,
                'countrySubdivisionId' => $california->id,
                'name' => ['EN' => 'Los Angeles County'],
                'code' => 'LA',
                'isoCode' => 'US-CA-LA',
                'shortName' => 'LA',
                'type' => CountrySubdivisionType::County,
                'officialLanguages' => ['EN'],
            ],
        ]);

        $collectionOfCounties = $countyData->map(fn (array $county): InsertCountrySubdivisionData => InsertCountrySubdivisionData::from($county));

        $this->action->handle($collectionOfCounties);

        /** @var CountrySubdivision $laCounty */
        $laCounty = CountrySubdivision::query()
            ->where('iso_code', 'US-CA-LA')
            ->first();

        expect($laCounty)
            ->not->toBeNull()
            ->and($laCounty->country_subdivision_id)
            ->toBe($california->id)
            ->and($laCounty->type)
            ->toBe(CountrySubdivisionType::County);
    });

test('handles empty subdivisions gracefully',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(CountrySubdivision::query()->count())->toBe(0);

        $this->action->handle($this->collectionOfInsertSubdivision);

        expect(CountrySubdivision::query()->count())->toBeGreaterThan(0);
    });
