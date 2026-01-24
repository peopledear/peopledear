<?php

declare(strict_types=1);

namespace App\Enums;

enum RecurringPeriod: int
{
    case Weekly = 1;
    case Monthly = 2;
    case Yearly = 3;

}
