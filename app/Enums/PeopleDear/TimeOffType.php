<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

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

    public function isAutomaticApproved(): bool
    {
        return match ($this) {
            self::SickLeave, self::Bereavement => true,
            self::Vacation, self::PersonalDay => false,
        };
    }
}
