<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeriodStatus;
use Carbon\CarbonImmutable;
use Database\Factories\PeriodFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read int $organization_id
 * @property-read int $year
 * @property-read CarbonImmutable $start
 * @property-read CarbonImmutable $end
 * @property-read PeriodStatus $status
 * @property-read Organization $organization
 */
final class Period extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<PeriodFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'year' => 'integer',
            'organization_id' => 'integer',
            'start' => 'immutable_date',
            'end' => 'immutable_date',
            'status' => PeriodStatus::class,
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    #[TenantRelation]
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
