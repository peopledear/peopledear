<?php

declare(strict_types=1);

namespace App\Enums;

enum LocationType: int
{
    case Headquarters = 1;
    case Branch = 2;
    case Warehouse = 3;
    case Store = 4;
    case Factory = 5;
    case Remote = 6;
    case Coworking = 7;

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
        return match ($this) {
            self::Headquarters => __('Headquarters'),
            self::Branch => __('Branch'),
            self::Warehouse => __('Warehouse'),
            self::Store => __('Store'),
            self::Factory => __('Factory'),
            self::Remote => __('Remote'),
            self::Coworking => __('Coworking'),
        };
    }

    public function icon(): Icon
    {
        return match ($this) {
            self::Headquarters, self::Branch => Icon::LucideBuilding,
            self::Warehouse => Icon::LucideGavel,
            self::Store => Icon::LucideUtensils,
            self::Factory => Icon::LucideBriefcase,
            self::Remote => Icon::LucideGlobe,
            self::Coworking => Icon::LucideUsers,
        };
    }
}
