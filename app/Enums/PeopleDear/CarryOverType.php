<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum CarryOverType: int
{
    case None = 1;
    case Unlimited = 2;
    case Limited = 3;

}
