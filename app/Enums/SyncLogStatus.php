<?php

declare(strict_types=1);

namespace App\Enums;

enum SyncLogStatus: int
{
    case Success = 1;
    case Failed = 2;
    case Partial = 3;
}
