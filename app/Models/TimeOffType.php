<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\AsArrayOfTimeOffUnit;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\TimeOffTypeStatus;
use App\Enums\TimeOffUnit;
use Carbon\CarbonImmutable;
use Database\Factories\TimeOffTypeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property string $organization_id
 * @property int|null $fallback_approval_role_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_system
 * @property array<int, TimeOffUnit> $allowed_units
 * @property Icon $icon
 * @property string $color
 * @property TimeOffTypeStatus $status
 * @property bool $requires_approval
 * @property bool $requires_justification
 * @property bool $requires_justification_document
 * @property BalanceType $balance_mode
 * @property-read TimeOffTypeBalanceConfigData $balance_config
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read ?Role $fallbackApprovalRole
 */
final class TimeOffType extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<TimeOffTypeFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return array<string, mixed>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
            'organization_id' => 'string',
            'fallback_approval_role_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_system' => 'boolean',
            'allowed_units' => AsArrayOfTimeOffUnit::class,
            'icon' => Icon::class,
            'color' => 'string',
            'status' => TimeOffTypeStatus::class,
            'requires_approval' => 'boolean',
            'requires_justification' => 'boolean',
            'requires_justification_document' => 'boolean',
            'balance_mode' => BalanceType::class,
            'balance_config' => TimeOffTypeBalanceConfigData::class,
        ];
    }

    /**
     * @return BelongsTo<Role, $this>
     */
    public function fallbackApprovalRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'fallback_approval_role_id');
    }

    /**
     * @return HasMany<TimeOffRequest, $this>
     */
    public function timeOffRequests(): HasMany
    {
        return $this->hasMany(TimeOffRequest::class);
    }

    /** @return BelongsTo<Organization, $this> */
    #[TenantRelation]
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
