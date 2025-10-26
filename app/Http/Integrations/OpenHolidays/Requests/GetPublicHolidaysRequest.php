<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Requests;

use Carbon\CarbonImmutable;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetPublicHolidaysRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $countryIsoCode,
        private readonly CarbonImmutable $validFrom,
        private readonly CarbonImmutable $validTo,
        private readonly ?string $languageIsoCode = null,
        private readonly ?string $subdivisionCode = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/PublicHolidays';
    }

    public function resolveCacheDriver(): Driver
    {
        $driver = config('openholidays.cache.driver');
        $store = $driver ? cache()->store($driver) : cache()->store();

        return new LaravelCacheDriver($store);
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('openholidays.cache.ttl', 2592000);
    }

    public function defaultQuery(): array
    {
        $query = [
            'countryIsoCode' => $this->countryIsoCode,
            'validFrom' => $this->validFrom->toDateString(),
            'validTo' => $this->validTo->toDateString(),
        ];

        if ($this->languageIsoCode !== null) {
            $query['languageIsoCode'] = $this->languageIsoCode;
        }

        if ($this->subdivisionCode !== null) {
            $query['subdivisionCode'] = $this->subdivisionCode;
        }

        return $query;
    }
}
