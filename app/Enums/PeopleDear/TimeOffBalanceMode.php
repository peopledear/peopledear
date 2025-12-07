<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum TimeOffBalanceMode: int
{
    case None = 0;
    case Annual = 1;
    case PerEvent = 2;
    case Recurring = 3;
}
