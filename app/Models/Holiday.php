<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\HolidayType;
use App\Models\Concerns\BelongsToOrganization;
use App\Models\Scopes\OrganizationScope;
use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string $country_id
 * @property-read Carbon $date
 * @property-read array<string, string> $name
 * @property-read HolidayType $type
 * @property-read bool $nationwide
 * @property-read string|null $subdivision_code
 * @property-read string|null $api_holiday_id
 * @property-read bool $is_custom
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Country $country
 */
#[ScopedBy([OrganizationScope::class])]
final class Holiday extends Model
{
    use BelongsToOrganization;

    /** @use HasFactory<HolidayFactory> */
    use HasFactory;

    use HasUuids;

    /** @return BelongsTo<Country, $this> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'country_id' => 'string',
            'date' => 'date',
            'name' => 'array',
            'type' => HolidayType::class,
            'nationwide' => 'boolean',
            'subdivision_code' => 'string',
            'api_holiday_id' => 'string',
            'is_custom' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
