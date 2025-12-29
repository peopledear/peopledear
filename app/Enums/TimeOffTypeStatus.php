<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeOffTypeStatus: int
{
    case Pending = 1;
    case Active = 2;
    case Inactive = 3;

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    public function label(): string
    {
        return __('time_off_type_status.'.mb_strtolower($this->name));
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Active => 'green',
            self::Inactive => 'gray',
        };
    }
}
