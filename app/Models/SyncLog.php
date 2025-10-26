<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SyncJobType;
use App\Enums\SyncLogStatus;
use Database\Factories\SyncLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property Carbon $synced_at
 * @property int $organization_id
 * @property SyncJobType $job_type
 * @property SyncLogStatus $status
 * @property int $records_synced_count
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property-read Organization $organization
 */
final class SyncLog extends Model
{
    /** @use HasFactory<SyncLogFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'synced_at' => 'datetime',
            'organization_id' => 'integer',
            'job_type' => SyncJobType::class,
            'status' => SyncLogStatus::class,
            'records_synced_count' => 'integer',
            'error_message' => 'string',
            'metadata' => 'array',
        ];
    }
}
