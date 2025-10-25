<?php

declare(strict_types=1);

namespace App\Enums;

enum OfficeType: int
{
    case Headquarters = 1;
    case Branch = 2;
    case Warehouse = 3;
    case Store = 4;
    case Factory = 5;
    case Remote = 6;
    case Coworking = 7;
    case HomeOffice = 8;

    /**
     * Get all office types as an array of value => label.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }

    /**
     * Get the display label for the office type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Headquarters => __('Headquarters'),
            self::Branch => __('Branch'),
            self::Warehouse => __('Warehouse'),
            self::Store => __('Store'),
            self::Factory => __('Factory'),
            self::Remote => __('Remote'),
            self::Coworking => __('Coworking'),
            self::HomeOffice => __('Home Office'),
        };
    }

    /**
     * Get the Lucide icon name for the office type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Headquarters => 'building-2',
            self::Branch => 'building',
            self::Warehouse => 'warehouse',
            self::Store => 'store',
            self::Factory => 'factory',
            self::Remote => 'globe',
            self::Coworking => 'users',
            self::HomeOffice => 'home',
        };
    }
}
