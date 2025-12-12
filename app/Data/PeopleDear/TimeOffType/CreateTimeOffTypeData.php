<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use App\Enums\PeopleDear\TimeOffBalanceMode;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Enums\Support\TimeOffIcon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class CreateTimeOffTypeData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly bool $isSystem,
        /** @var array<int, TimeOffUnit> */
        public readonly array $allowedUnits,
        public readonly TimeOffIcon $icon,
        public readonly string $color,
        public readonly bool $isActive,
        public readonly bool $requiresApproval,
        public readonly bool $requiresJustification,
        public readonly bool $requiresJustificationDocument,
        public readonly TimeOffBalanceMode $balanceMode,
        public readonly null|TimeOffTypeBalanceConfigData|Optional $balanceConfig,
        public readonly ?string $description = null,
        public readonly ?int $fallbackApprovalRoleId = null,
    ) {}

}
