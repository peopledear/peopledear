<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum TimeOffUnit: int
{
    case Day = 1;
    case HalfDay = 2;
    case Hour = 3;
    case Minute = 4;

    public function label(): string
    {
        return match ($this) {
            self::Day => 'Day',
            self::HalfDay => 'Half Day',
            self::Hour => 'Hour',
            self::Minute => 'Minute',
        };
    }
}
