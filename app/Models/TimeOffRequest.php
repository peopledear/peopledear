<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Concerns\BelongsToEmployee;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\BelongsToPeriod;
use App\Models\Scopes\OrganizationScope;
use Carbon\Carbon;
use Database\Factories\TimeOffRequestFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string $employee_id
 * @property-read string $time_off_type_id
 * @property-read RequestStatus $status
 * @property-read Carbon $start_date
 * @property-read Carbon|null $end_date
 * @property-read bool $is_half_day
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Employee $employee
 * @property-read TimeOffType $type
 */
#[ScopedBy([OrganizationScope::class])]
final class TimeOffRequest extends Model
{
    use BelongsToEmployee;
    use BelongsToOrganization;
    use BelongsToPeriod;

    /** @use HasFactory<TimeOffRequestFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'employee_id' => 'string',
            'time_off_type_id' => 'string',
            'status' => RequestStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_half_day' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<TimeOffType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TimeOffType::class, 'time_off_type_id');
    }
}
