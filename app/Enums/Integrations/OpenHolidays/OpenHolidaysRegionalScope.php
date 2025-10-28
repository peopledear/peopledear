<?php

declare(strict_types=1);

namespace App\Enums\Integrations\OpenHolidays;

enum OpenHolidaysRegionalScope: string
{
    case National = 'National';
    case Regional = 'Regional';
    case Local = 'Local';

}
