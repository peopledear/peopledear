<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Carbon\Carbon;
use Database\Factories\ApprovalFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property-read int $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read int $organization_id
 * @property-read int|null $approved_by
 * @property-read string $approvable_type
 * @property-read int $approvable_id
 * @property-read RequestStatus $status
 * @property-read Carbon|null $approved_at
 * @property-read string|null $rejection_reason
 * @property-read Organization $organization
 * @property-read Employee|null $approver
 * @property-read Model $approvable
 */
#[ScopedBy([OrganizationScope::class])]
final class Approval extends Model
{
    use BelongsToOrganization;

    /** @use HasFactory<ApprovalFactory> */
    use HasFactory;

    /**
     * @return MorphTo<Model, $this>
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'approved_by' => 'integer',
            'approvable_id' => 'integer',
            'status' => RequestStatus::class,
            'approved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
