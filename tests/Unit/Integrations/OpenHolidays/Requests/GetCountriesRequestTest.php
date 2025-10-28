<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Requests\GetCountriesRequest;
use Illuminate\Support\Facades\Config;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\Enums\Method;

test('request has correct method', function (): void {
    $request = new GetCountriesRequest;

    expect($request->getMethod())->toBe(Method::GET);
});

test('request resolves correct endpoint', function (): void {
    $request = new GetCountriesRequest;

    expect($request->resolveEndpoint())->toBe('/Countries');
});

test('request has empty query when no language provided', function (): void {
    $request = new GetCountriesRequest;

    expect($request->defaultQuery())
        ->toBeArray()
        ->toBeEmpty();
});

test('request includes language parameter when provided', function (): void {
    $request = new GetCountriesRequest(languageIsoCode: 'pt');

    $query = $request->defaultQuery();

    expect($query)
        ->toBeArray()
        ->toHaveKey('languageIsoCode')
        ->and($query['languageIsoCode'])
        ->toBe('pt');
});

test('request uses cache driver from config', function (): void {
    $request = new GetCountriesRequest;

    expect($request->resolveCacheDriver())->toBeInstanceOf(LaravelCacheDriver::class);
});

test('request uses cache ttl from config', function (): void {
    Config::set('openholidays.cache.ttl', 7200);

    $request = new GetCountriesRequest;

    expect($request->cacheExpiryInSeconds())->toBe(7200);
});
