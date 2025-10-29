<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\HolidayType;
use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property-read int $organization_id
 * @property-read int $country_id
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
final class Holiday extends Model
{
    /** @use HasFactory<HolidayFactory> */
    use HasFactory;

    /** @return BelongsTo<Organization, $this> */
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
            'id' => 'integer',
            'organization_id' => 'integer',
            'country_id' => 'integer',
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
