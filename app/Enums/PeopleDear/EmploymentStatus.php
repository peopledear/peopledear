<?php

declare(strict_types=1);

namespace App\Enums\PeopleDear;

enum EmploymentStatus: int
{
    case Active = 1;
    case Inactive = 2;
    case OnLeave = 3;
    case Terminated = 4;

    /**
     * Get all employment statuses as an array of value => label.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    /**
     * Get the display label for the employment status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => __('employment_status.active'),
            self::Inactive => __('employment_status.inactive'),
            self::OnLeave => __('employment_status.on_leave'),
            self::Terminated => __('employment_status.terminated'),
        };
    }
}
