<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum HolidayType: int
{
    case Public = 1;
    case Regional = 2;
    case Local = 3;
    case School = 4;
    case Bank = 5;
    case Optional = 6;
    case Observance = 7;
    case Custom = 8;
}
