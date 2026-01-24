<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RequestStatus;
use App\Models\Concerns\BelongsToEmployee;
use App\Models\Concerns\BelongsToPeriod;
use Carbon\CarbonImmutable;
use Database\Factories\TimeOffRequestFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string $employee_id
 * @property-read string $time_off_type_id
 * @property-read RequestStatus $status
 * @property-read CarbonImmutable $start_date
 * @property-read CarbonImmutable|null $end_date
 * @property-read bool $is_half_day
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Organization $organization
 * @property-read Employee $employee
 */
final class TimeOffRequest extends Model
{
    use BelongsToEmployee;
    use BelongsToPeriod;
    use BelongsToTenant;

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
            'start_date' => 'immutable_date',
            'end_date' => 'immutable_date',
            'is_half_day' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<TimeOffType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TimeOffType::class, 'time_off_type_id');
    }

    /** @return BelongsTo<Organization, $this> */
    #[TenantRelation]
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
