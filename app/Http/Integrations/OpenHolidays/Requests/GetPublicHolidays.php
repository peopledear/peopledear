<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Requests;

use App\Data\Integrations\OpenHolidays\OpenHolidaysHolidayData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetPublicHolidays extends Request implements Cacheable
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
        $driver = config()->string('openholidays.cache.driver', 'database');

        return new LaravelCacheDriver(cache()->store($driver));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config()->integer('openholidays.cache.ttl', 2592000);
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

    /**
     * @return Collection<int, OpenHolidaysHolidayData>
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {

        $data = $response->json();

        return OpenHolidaysHolidayData::collect($data, Collection::class);
    }
}
