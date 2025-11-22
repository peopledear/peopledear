<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Carbon\Carbon;
use Database\Factories\TimeOffRequestFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $organization_id
 * @property-read int $employee_id
 * @property-read TimeOffType $type
 * @property-read RequestStatus $status
 * @property-read Carbon $start_date
 * @property-read Carbon|null $end_date
 * @property-read bool $is_half_day
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Employee $employee
 */
#[ScopedBy([OrganizationScope::class])]
final class TimeOffRequest extends Model
{
    use BelongsToOrganization;

    /** @use HasFactory<TimeOffRequestFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'employee_id' => 'integer',
            'type' => TimeOffType::class,
            'status' => RequestStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_half_day' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
