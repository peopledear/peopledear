<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum RequestStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case Rejected = 3;
    case Cancelled = 4;

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
        return __('time_off_status.'.mb_strtolower($this->name));
    }
}
