<?php

declare(strict_types=1);

namespace App\Enums;

use App\Data\BalanceTypeData;
use Illuminate\Support\Collection;

use function collect;

enum BalanceType: int
{
    case None = 0;
    case Annual = 1;
    case PerEvent = 2;
    case Recurring = 3;

    /**
     * @return Collection<int, BalanceTypeData>
     */
    public static function options(): Collection
    {

        return BalanceTypeData::collect(
            collect(self::cases())->map(fn (BalanceType $type): array => [
                'value' => $type->value,
                'label' => $type->label(),
            ]), Collection::class);

    }

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Annual => 'Annual',
            self::PerEvent => 'Per Event',
            self::Recurring => 'Recurring',
        };
    }
}
