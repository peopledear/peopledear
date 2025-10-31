<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Requests\GetCountries;
use Illuminate\Support\Facades\Config;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\Enums\Method;

test('request has correct method', function (): void {
    $request = new GetCountries;

    expect($request->getMethod())->toBe(Method::GET);
});

test('request resolves correct endpoint', function (): void {
    $request = new GetCountries;

    expect($request->resolveEndpoint())->toBe('/Countries');
});

test('request has empty query when no language provided', function (): void {
    $request = new GetCountries;

    expect($request->defaultQuery())
        ->toBeArray()
        ->toBeEmpty();
});

test('request includes language parameter when provided', function (): void {
    $request = new GetCountries(languageIsoCode: 'pt');

    $query = $request->defaultQuery();

    expect($query)
        ->toBeArray()
        ->toHaveKey('languageIsoCode')
        ->and($query['languageIsoCode'])
        ->toBe('pt');
});

test('request uses cache driver from config', function (): void {
    $request = new GetCountries;

    expect($request->resolveCacheDriver())->toBeInstanceOf(LaravelCacheDriver::class);
});

test('request uses cache ttl from config', function (): void {
    Config::set('openholidays.cache.ttl', 7200);

    $request = new GetCountries;

    expect($request->cacheExpiryInSeconds())->toBe(7200);
});
