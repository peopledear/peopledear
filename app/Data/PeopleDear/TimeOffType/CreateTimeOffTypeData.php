<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\TimeOffType;

use App\Enums\BalanceType;
use App\Enums\Icon;
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
        /** @var array<int, int> $allowedUnits */
        public readonly array $allowedUnits,
        public readonly Icon $icon,
        public readonly string $color,
        public readonly bool $requiresApproval,
        public readonly bool $requiresJustification,
        public readonly bool $requiresJustificationDocument,
        public readonly BalanceType $balanceMode,
        public readonly null|TimeOffTypeBalanceConfigData|Optional $balanceConfig,
        public readonly bool $isSystem = false,
        public readonly ?string $description = null,
        public readonly ?int $fallbackApprovalRoleId = null,
    ) {}

}
