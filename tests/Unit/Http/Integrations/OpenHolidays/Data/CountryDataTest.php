<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Data\CountryData;
use Illuminate\Support\Facades\Config;

test('country data can be created with all fields', function (): void {
    $data = new CountryData(
        isoCode: 'US',
        name: ['en' => 'United States', 'pt' => 'Estados Unidos'],
        officialLanguages: ['en'],
    );

    expect($data->isoCode)
        ->toBe('US')
        ->and($data->name)
        ->toBe(['en' => 'United States', 'pt' => 'Estados Unidos'])
        ->and($data->officialLanguages)
        ->toBe(['en']);
});

test('get localized name returns specified language', function (): void {
    $data = new CountryData(
        isoCode: 'PT',
        name: ['en' => 'Portugal', 'pt' => 'Portugal', 'es' => 'Portugal'],
        officialLanguages: ['pt'],
    );

    expect($data->getLocalizedName('en'))
        ->toBe('Portugal')
        ->and($data->getLocalizedName('pt'))
        ->toBe('Portugal');
});

test('get localized name falls back to en when language not found', function (): void {
    $data = new CountryData(
        isoCode: 'US',
        name: ['en' => 'United States'],
        officialLanguages: ['en'],
    );

    expect($data->getLocalizedName('fr'))->toBe('United States');
});

test('get localized name uses default language from config', function (): void {
    Config::set('openholidays.default_language', 'pt');

    $data = new CountryData(
        isoCode: 'BR',
        name: ['en' => 'Brazil', 'pt' => 'Brasil'],
        officialLanguages: ['pt'],
    );

    expect($data->getLocalizedName())->toBe('Brasil');
});

test('get localized name falls back to first available when no match', function (): void {
    $data = new CountryData(
        isoCode: 'DE',
        name: ['de' => 'Deutschland', 'fr' => 'Allemagne'],
        officialLanguages: ['de'],
    );

    expect($data->getLocalizedName('en'))->toBe('Deutschland');
});

test('get localized name falls back to iso code when name is empty', function (): void {
    $data = new CountryData(
        isoCode: 'XX',
        name: [],
        officialLanguages: [],
    );

    expect($data->getLocalizedName())->toBe('XX');
});
