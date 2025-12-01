<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum PeriodStatus: int
{
    case Active = 1;
    case Closed = 2;

}
