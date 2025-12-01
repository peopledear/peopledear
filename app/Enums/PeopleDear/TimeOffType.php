<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

use App\Enums\Support\TimeOffIcon;

enum TimeOffType: int
{
    case Vacation = 1;
    case SickLeave = 2;
    case PersonalDay = 3;
    case Bereavement = 4;

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }

    public function label(): string
    {
        return __('time_off_type.'.mb_strtolower($this->name));
    }

    public function icon(): TimeOffIcon
    {
        return match ($this) {
            self::Vacation => TimeOffIcon::Plane,
            self::SickLeave => TimeOffIcon::HeartPulse,
            self::PersonalDay => TimeOffIcon::House,
            self::Bereavement => TimeOffIcon::EyeOff,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Vacation => 'blue',
            self::SickLeave => 'red',
            self::PersonalDay => 'purple',
            self::Bereavement => 'gray',
        };
    }

    public function isAutomaticApproved(): bool
    {
        return match ($this) {
            self::SickLeave, self::Bereavement => true,
            self::Vacation, self::PersonalDay => false,
        };
    }
}
