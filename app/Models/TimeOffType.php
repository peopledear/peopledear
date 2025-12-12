<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\AsArrayOfTimeOffUnit;
use App\Data\PeopleDear\TimeOffType\TimeOffTypeBalanceConfigData;
use App\Enums\PeopleDear\TimeOffBalanceMode;
use App\Enums\PeopleDear\TimeOffUnit;
use App\Enums\Support\TimeOffIcon;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Carbon\Carbon;
use Database\Factories\TimeOffTypeFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

/**
 * @property-read string $id
 * @property string $organization_id
 * @property int|null $fallback_approval_role_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_system
 * @property array<int, TimeOffUnit> $allowed_units
 * @property TimeOffIcon $icon
 * @property string $color
 * @property bool $is_active
 * @property bool $requires_approval
 * @property bool $requires_justification
 * @property bool $requires_justification_document
 * @property TimeOffBalanceMode $balance_mode
 * @property-read TimeOffTypeBalanceConfigData $balance_config
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 * @property-read ?Role $fallbackApprovalRole
 */
#[ScopedBy(OrganizationScope::class)]
final class TimeOffType extends Model
{
    use BelongsToOrganization;

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'organization_id' => 'string',
            'fallback_approval_role_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_system' => 'boolean',
            'allowed_units' => AsArrayOfTimeOffUnit::class,
            'icon' => TimeOffIcon::class,
            'color' => 'string',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
            'requires_justification' => 'boolean',
            'requires_justification_document' => 'boolean',
            'balance_mode' => TimeOffBalanceMode::class,
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
}
