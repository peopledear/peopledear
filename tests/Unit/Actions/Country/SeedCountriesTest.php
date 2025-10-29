<?php

declare(strict_types=1);

use App\Actions\Country\SeedCountries;
use App\Models\Country;

test('seeds all countries from json file', function (): void {
    Country::query()->delete();

    expect(Country::query()->count())->toBe(0);

    $action = new SeedCountries;

    $action->handle();

    expect(Country::query()->count())->toBe(36);
});

test('upserts countries idempotently', function (): void {
    $initialCount = Country::query()->count();

    expect($initialCount)->toBe(36);

    $action = new SeedCountries;

    $action->handle();

    expect(Country::query()->count())->toBe(36);
});

test('updates existing country data', function (): void {
    Country::query()
        ->where('iso_code', 'PT')
        ->update(['name' => json_encode(['en' => 'Modified'])]);

    /** @var Country $modified */
    $modified = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    expect($modified->name)->toBe(['en' => 'Modified']);

    $action = new SeedCountries;

    $action->handle();

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

test('preserves country id when upserting', function (): void {
    /** @var Country $portugal */
    $portugal = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    $originalId = $portugal->id;

    $action = new SeedCountries;

    $action->handle();

    /** @var Country $after */
    $after = Country::query()
        ->where('iso_code', 'PT')
        ->first();

    expect($after->id)->toBe($originalId);
});

test('seeds countries with correct structure', function (): void {
    Country::query()->delete();

    $action = new SeedCountries;

    $action->handle();

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

test('seeds multilingual countries correctly', function (): void {
    Country::query()->delete();

    $action = new SeedCountries;

    $action->handle();

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

test('handles empty countries gracefully', function (): void {
    Country::query()->delete();

    expect(Country::query()->count())->toBe(0);

    $action = new SeedCountries;

    $action->handle();

    expect(Country::query()->count())->toBeGreaterThan(0);
});

test('skips non-array entries in json', function (): void {
    $originalPath = database_path('data/countries.json');
    $backupPath = database_path('data/countries_backup.json');

    rename($originalPath, $backupPath);

    file_put_contents($originalPath, json_encode([
        ['isoCode' => 'XX', 'name' => ['en' => 'Test'], 'officialLanguages' => ['en']],
        'invalid entry',
        ['isoCode' => 'YY', 'name' => ['en' => 'Test2'], 'officialLanguages' => ['en']],
        123,
        ['isoCode' => 'ZZ', 'name' => ['en' => 'Test3'], 'officialLanguages' => ['en']],
    ]));

    try {
        Country::query()->delete();

        $action = new SeedCountries;

        $action->handle();

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
