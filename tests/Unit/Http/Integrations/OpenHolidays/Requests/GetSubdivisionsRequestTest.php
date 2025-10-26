<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Requests\GetSubdivisionsRequest;
use Illuminate\Support\Facades\Config;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\Enums\Method;

test('request has correct method', function (): void {
    $request = new GetSubdivisionsRequest(countryIsoCode: 'US');

    expect($request->getMethod())->toBe(Method::GET);
});

test('request resolves correct endpoint', function (): void {
    $request = new GetSubdivisionsRequest(countryIsoCode: 'US');

    expect($request->resolveEndpoint())->toBe('/Subdivisions');
});

test('request has required country parameter', function (): void {
    $request = new GetSubdivisionsRequest(countryIsoCode: 'PT');

    $query = $request->defaultQuery();

    expect($query)
        ->toBeArray()
        ->toHaveKey('countryIsoCode')
        ->and($query['countryIsoCode'])
        ->toBe('PT');
});

test('request includes language parameter when provided', function (): void {
    $request = new GetSubdivisionsRequest(
        countryIsoCode: 'PT',
        languageIsoCode: 'pt',
    );

    $query = $request->defaultQuery();

    expect($query)
        ->toHaveKey('languageIsoCode')
        ->and($query['languageIsoCode'])
        ->toBe('pt');
});

test('request excludes language parameter when not provided', function (): void {
    $request = new GetSubdivisionsRequest(countryIsoCode: 'US');

    $query = $request->defaultQuery();

    expect($query)->not->toHaveKey('languageIsoCode');
});

test('request uses cache driver from config', function (): void {
    $request = new GetSubdivisionsRequest(countryIsoCode: 'US');

    expect($request->resolveCacheDriver())->toBeInstanceOf(LaravelCacheDriver::class);
});

test('request uses cache ttl from config', function (): void {
    Config::set('openholidays.cache.ttl', 1800);

    $request = new GetSubdivisionsRequest(countryIsoCode: 'US');

    expect($request->cacheExpiryInSeconds())->toBe(1800);
});
