<?php

declare(strict_types=1);

namespace App\Enums;

enum PeriodStatus: int
{
    case Active = 1;
    case Closed = 2;

}
