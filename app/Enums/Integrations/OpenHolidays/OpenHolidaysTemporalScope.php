<?php

declare(strict_types=1);

namespace App\Enums\Integrations\OpenHolidays;

enum OpenHolidaysTemporalScope: string
{
    case FullDay = 'FullDay';
    case HalfDay = 'HalfDay';
}
