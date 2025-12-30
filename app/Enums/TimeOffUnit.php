<?php

declare(strict_types=1);

namespace App\Enums;

use App\Data\PeopleDear\TimeOffType\TimeOffUnitData;
use Illuminate\Support\Collection;

use function collect;

enum TimeOffUnit: int
{
    case Day = 1;
    case HalfDay = 2;
    case Hour = 3;
    case Minute = 4;

    /**
     * @return Collection<int, TimeOffUnitData>
     */
    public static function options(): Collection
    {
        return collect(self::cases())
            ->map(fn (TimeOffUnit $unit): TimeOffUnitData => TimeOffUnitData::from([
                'value' => $unit->value,
                'label' => $unit->label(),
            ]));
    }

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
