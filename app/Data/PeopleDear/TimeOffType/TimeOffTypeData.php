<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use App\Data\CastsAndTransformers\AsIconDataTransformer;
use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\TimeOffTypeStatus;
use App\Enums\TimeOffUnit;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class TimeOffTypeData extends Data
{
    /**
     * @param  array<int, TimeOffUnit>  $allowedUnits
     */
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly Optional|int $fallbackApprovalRoleId,
        public readonly string $name,
        public readonly Optional|string $description,
        public readonly bool $isSystem,
        public readonly array $allowedUnits,
        #[WithCast(EnumCast::class, Icon::class)]
        #[WithTransformer(AsIconDataTransformer::class)]
        public readonly Icon $icon,
        public readonly string $color,
        #[WithCast(EnumCast::class, TimeOffTypeStatus::class)]
        public readonly TimeOffTypeStatus $status,
        public readonly bool $requiresApproval,
        public readonly bool $requiresJustification,
        public readonly bool $requiresJustificationDocument,
        #[WithCast(EnumCast::class, BalanceType::class)]
        public readonly BalanceType $balanceMode,
        public readonly ?TimeOffTypeBalanceConfigData $balanceConfig,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
    ) {}

}
