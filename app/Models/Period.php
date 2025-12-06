<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Database\Factories\PeriodFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read int $organization_id
 * @property-read int $year
 * @property-read Carbon $start
 * @property-read Carbon $end
 * @property-read PeriodStatus $status
 * @property-read Organization $organization
 */
#[ScopedBy([OrganizationScope::class])]
final class Period extends Model
{
    use BelongsToOrganization;

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
            'start' => 'date',
            'end' => 'date',
            'status' => PeriodStatus::class,
        ];
    }
}
