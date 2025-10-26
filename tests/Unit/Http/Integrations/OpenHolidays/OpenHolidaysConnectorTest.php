<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\OpenHolidaysConnector;
use Illuminate\Support\Facades\Config;

beforeEach(function (): void {

    $this->connector = new OpenHolidaysConnector();
});

test('connector resolves base url from config', function (): void {
    Config::set('openholidays.api_url', 'https://test-api.example.com');

    expect($this->connector->resolveBaseUrl())
        ->toBe('https://test-api.example.com');
});

test('connector has correct default headers', function (): void {

    expect($this->connector->defaultHeaders())
        ->toBeArray()
        ->toHaveKey('Accept')
        ->and($this->connector->defaultHeaders()['Accept'])
        ->toBe('application/json');
});

test('connector has correct default config with timeouts', function (): void {
    Config::set('openholidays.timeout.request', 15);
    Config::set('openholidays.timeout.connect', 10);

    $config = $this->connector->defaultConfig();

    expect($config)
        ->toBeArray()
        ->toHaveKey('timeout')
        ->toHaveKey('connect_timeout')
        ->and($config['timeout'])
        ->toBe(15)
        ->and($config['connect_timeout'])
        ->toBe(10);
});

test('connector uses default timeout values when config not set', function (): void {

    $config = $this->connector->defaultConfig();

    expect($config['timeout'])
        ->toBe(10)
        ->and($config['connect_timeout'])
        ->toBe(5);
});
