<?php

declare(strict_types=1);

return [
    'api_url' => env('OPENHOLIDAYS_API_URL', 'https://openholidaysapi.org'),

    'cache' => [
        'enabled' => env('OPENHOLIDAYS_CACHE_ENABLED', true),
        'ttl' => env('OPENHOLIDAYS_CACHE_TTL', 2592000), // 30 days
        'driver' => env('OPENHOLIDAYS_CACHE_DRIVER', 'database'),
    ],

    'included_types' => [
        'Public',
        'Regional',
        'Local',
        'Optional',
    ],

    'default_language' => env('OPENHOLIDAYS_DEFAULT_LANGUAGE', 'en'),

    'fallback_on_error' => env('OPENHOLIDAYS_FALLBACK', 'allow'),

    'timeout' => [
        'connect' => 5,
        'request' => 10,
    ],

    'sync' => [
        'months_ahead' => 12,
        'schedule_time' => '02:00',
        'schedule_day' => 1, // 1st of month
    ],

    /**
     * Rate limiting configuration for API requests
     *
     * Controls the delay between consecutive API requests to prevent
     * hitting OpenHolidays API rate limits during batch operations
     * like fetching subdivisions for multiple countries.
     */
    'rate_limit' => [
        /**
         * Delay in milliseconds between API requests
         *
         * Default: 500ms (0.5 seconds) between requests
         * Adjust based on API rate limit requirements and environment
         */
        'delay_ms' => env('OPENHOLIDAYS_RATE_LIMIT_DELAY_MS', 500),
    ],
];
