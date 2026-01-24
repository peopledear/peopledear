<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\HolidayType;
use Carbon\CarbonImmutable;
use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

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
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Organization $organization
 * @property-read Country $country
 */
final class Holiday extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<HolidayFactory> */
    use HasFactory;

    use HasUuids;

    /** @return BelongsTo<Organization, $this> */
    #[TenantRelation]
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
