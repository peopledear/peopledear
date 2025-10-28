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
];
