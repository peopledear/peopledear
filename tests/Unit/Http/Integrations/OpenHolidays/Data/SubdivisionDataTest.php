<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Data\SubdivisionData;
use Illuminate\Support\Facades\Config;

test('subdivision data can be created with all fields', function (): void {
    $data = new SubdivisionData(
        isoCode: 'US-CA',
        shortName: 'CA',
        name: ['en' => 'California', 'pt' => 'Califórnia'],
        officialLanguages: 'en',
        children: [['isoCode' => 'US-CA-SF', 'name' => 'San Francisco']],
    );

    expect($data->isoCode)
        ->toBe('US-CA')
        ->and($data->shortName)
        ->toBe('CA')
        ->and($data->name)
        ->toBe(['en' => 'California', 'pt' => 'Califórnia'])
        ->and($data->officialLanguages)
        ->toBe('en')
        ->and($data->children)
        ->toBeArray()
        ->toHaveCount(1);
});

test('subdivision data can be created with minimal fields', function (): void {
    $data = new SubdivisionData(
        isoCode: 'US-NY',
        shortName: 'NY',
        name: ['en' => 'New York'],
    );

    expect($data->isoCode)
        ->toBe('US-NY')
        ->and($data->officialLanguages)
        ->toBeNull()
        ->and($data->children)
        ->toBeNull();
});

test('get localized name returns specified language', function (): void {
    $data = new SubdivisionData(
        isoCode: 'PT-11',
        shortName: 'Lisboa',
        name: ['en' => 'Lisbon', 'pt' => 'Lisboa', 'es' => 'Lisboa'],
    );

    expect($data->getLocalizedName('en'))
        ->toBe('Lisbon')
        ->and($data->getLocalizedName('pt'))
        ->toBe('Lisboa');
});

test('get localized name falls back to en when language not found', function (): void {
    $data = new SubdivisionData(
        isoCode: 'US-CA',
        shortName: 'CA',
        name: ['en' => 'California'],
    );

    expect($data->getLocalizedName('fr'))->toBe('California');
});

test('get localized name uses default language from config', function (): void {
    Config::set('openholidays.default_language', 'pt');

    $data = new SubdivisionData(
        isoCode: 'PT-11',
        shortName: 'Lisboa',
        name: ['en' => 'Lisbon', 'pt' => 'Lisboa'],
    );

    expect($data->getLocalizedName())->toBe('Lisboa');
});

test('get localized name falls back to first available when no match', function (): void {
    $data = new SubdivisionData(
        isoCode: 'DE-BY',
        shortName: 'BY',
        name: ['de' => 'Bayern', 'fr' => 'Bavière'],
    );

    expect($data->getLocalizedName('en'))->toBe('Bayern');
});

test('get localized name falls back to short name when name is empty', function (): void {
    $data = new SubdivisionData(
        isoCode: 'XX-YY',
        shortName: 'YY',
        name: [],
    );

    expect($data->getLocalizedName())->toBe('YY');
});
