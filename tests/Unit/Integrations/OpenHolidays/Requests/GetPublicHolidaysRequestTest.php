<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\Requests\GetPublicHolidaysRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\Enums\Method;

test('request has correct method', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    expect($request->getMethod())->toBe(Method::GET);
});

test('request resolves correct endpoint', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    expect($request->resolveEndpoint())->toBe('/PublicHolidays');
});

test('request has required query parameters', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    $query = $request->defaultQuery();

    expect($query)
        ->toBeArray()
        ->toHaveKey('countryIsoCode')
        ->toHaveKey('validFrom')
        ->toHaveKey('validTo')
        ->and($query['countryIsoCode'])
        ->toBe('US')
        ->and($query['validFrom'])
        ->toBe('2025-01-01')
        ->and($query['validTo'])
        ->toBe('2025-12-31');
});

test('request includes optional language parameter when provided', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'PT',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
        languageIsoCode: 'pt',
    );

    $query = $request->defaultQuery();

    expect($query)
        ->toHaveKey('languageIsoCode')
        ->and($query['languageIsoCode'])
        ->toBe('pt');
});

test('request includes optional subdivision parameter when provided', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
        subdivisionCode: 'CA',
    );

    $query = $request->defaultQuery();

    expect($query)
        ->toHaveKey('subdivisionCode')
        ->and($query['subdivisionCode'])
        ->toBe('CA');
});

test('request excludes optional parameters when not provided', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    $query = $request->defaultQuery();

    expect($query)
        ->not->toHaveKey('languageIsoCode')
        ->not->toHaveKey('subdivisionCode');
});

test('request uses cache driver from config', function (): void {
    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    expect($request->resolveCacheDriver())->toBeInstanceOf(LaravelCacheDriver::class);
});

test('request uses cache ttl from config', function (): void {
    Config::set('openholidays.cache.ttl', 3600);

    $request = new GetPublicHolidaysRequest(
        countryIsoCode: 'US',
        validFrom: CarbonImmutable::parse('2025-01-01'),
        validTo: CarbonImmutable::parse('2025-12-31'),
    );

    expect($request->cacheExpiryInSeconds())->toBe(3600);
});
