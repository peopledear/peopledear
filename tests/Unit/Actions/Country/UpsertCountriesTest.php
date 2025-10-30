<?php

declare(strict_types=1);

use App\Actions\Country\UpsertCountries;
use App\Data\PeopleDear\Country\InsertCountryData;
use App\Models\Country;

/**
 * @throws Throwable
 */
beforeEach(function (): void {
    $contents = file_get_contents(database_path('data/countries.json'));
    $this->countries = collect(json_decode($contents, true));
    /** @var UpsertCountries $this action */
    $this->action = app(UpsertCountries::class);
    $this->collectionOfInsertCountry = $this->countries->map(fn(array $country): InsertCountryData => InsertCountryData::from($country));
});

test('seeds all countries from json file',
    /**
     * @throws Throwable
     */
    function (): void {
        expect(Country::query()->count())->toBe(0);

        $this->action->handle($this->collectionOfInsertCountry);

        expect(Country::query()->count())
            ->toBe($this->countries->count());
    });

test('upserts countries idempotently',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertCountry);

        $initialCount = Country::query()->count();

        expect($initialCount)->toBe($this->countries->count());

        $this->action->handle($this->collectionOfInsertCountry);

        expect(Country::query()->count())->toBe($initialCount);
    });

test('updates existing country data',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->action->handle($this->collectionOfInsertCountry);

        Country::query()
            ->where('iso_code', 'PT')
            ->update(['name' => json_encode(['en' => 'Modified'])]);

        /** @var Country $modified */
        $modified = Country::query()
            ->where('iso_code', 'PT')
            ->first();

        expect($modified->name)->toBe(['en' => 'Modified']);

        $this->action->handle($this->collectionOfInsertCountry);

        /** @var Country $updated */
        $updated = Country::query()
            ->where('iso_code', 'PT')
            ->first()
            ?->fresh();

        expect($updated->name)
            ->not->toBe(['en' => 'Modified'])
            ->and($updated->name['EN'])
            ->toBe('Portugal');
    });

/**
 * @throws Throwable
 */
test('preserves country id when upserting', function (): void {
    $this->action->handle($this->collectionOfInsertCountry);

    /** @var Country $portugal */
    $portugal = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    $originalId = $portugal->id;

    $this->action->handle($this->collectionOfInsertCountry);

    /** @var Country $after */
    $after = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    expect($after->id)->toBe($originalId);
});

/**
 * @throws Throwable
 */
test('seeds countries with correct structure', function (): void {
    $this->action->handle($this->collectionOfInsertCountry);

    /** @var Country $portugal */
    $portugal = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    expect($portugal)
        ->not->toBeNull()
        ->and($portugal->iso_code)
        ->toBe('PT')
        ->and($portugal->name)
        ->toBeArray()
        ->and($portugal->official_languages)
        ->toBeArray()
        ->and($portugal->official_languages)
        ->toBe(['PT']);
});

/**
 * @throws Throwable
 */
test('seeds multilingual countries correctly', function (): void {
    $this->action->handle($this->collectionOfInsertCountry);

    /** @var Country $belgium */
    $belgium = Country::query()
        ->where('iso_code', 'BE')
        ->first();

    expect($belgium)
        ->not->toBeNull()
        ->and($belgium->name['EN'])
        ->toBe('Belgium')
        ->and($belgium->name['NL'])
        ->toBe('België')
        ->and($belgium->official_languages)
        ->toBe(['NL', 'FR', 'DE'])
        ->and($belgium->official_languages)
        ->toHaveCount(3);
});

/**
 * @throws Throwable
 */
test('handles empty countries gracefully', function (): void {
    expect(Country::query()->count())->toBe(0);

    $this->action->handle($this->collectionOfInsertCountry);

    expect(Country::query()->count())->toBeGreaterThan(0);
});

/**
 * @throws Throwable
 */
test('skips non-array entries in json', function (): void {
    $originalPath = database_path('data/countries.json');
    $backupPath = database_path('data/countries_backup.json');

    rename($originalPath, $backupPath);

    $testData = [
        ['isoCode' => 'XX', 'name' => ['en' => 'Test'], 'officialLanguages' => ['en']],
        'invalid entry',
        ['isoCode' => 'YY', 'name' => ['en' => 'Test2'], 'officialLanguages' => ['en']],
        123,
        ['isoCode' => 'ZZ', 'name' => ['en' => 'Test3'], 'officialLanguages' => ['en']],
    ];

    file_put_contents($originalPath, json_encode($testData));

    try {
        $validCountries = collect($testData)
            ->filter(fn($item): bool => is_array($item))
            ->map(fn(array $country): InsertCountryData => InsertCountryData::from($country));

        $this->action->handle($validCountries);

        expect(Country::query()->count())->toBe(3);

        $isoCodes = Country::query()
            ->pluck('iso_code')
            ->toArray();

        expect($isoCodes)->toBe(['XX', 'YY', 'ZZ']);
    } finally {
        unlink($originalPath);
        rename($backupPath, $originalPath);
    }
});
