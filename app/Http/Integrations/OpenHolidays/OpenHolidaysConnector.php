<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

final class OpenHolidaysConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return config()->string('openholidays.api_url');
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => config()->integer('openholidays.timeout.request', 10),
            'connect_timeout' => config()->integer('openholidays.timeout.connect', 5),
        ];
    }
}
