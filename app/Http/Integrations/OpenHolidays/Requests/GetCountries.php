<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Requests;

use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetCountries extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $languageIsoCode = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Countries';
    }

    public function resolveCacheDriver(): Driver
    {
        $driver = config()->string('openholidays.cache.driver', 'database');

        return new LaravelCacheDriver(cache()->store($driver));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config()->integer('openholidays.cache.ttl', 2592000);
    }

    public function defaultQuery(): array
    {
        if ($this->languageIsoCode === null) {
            return [];
        }

        return [
            'languageIsoCode' => $this->languageIsoCode,
        ];
    }
}
