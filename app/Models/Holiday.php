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
 * @property int $organization_id
 * @property Carbon $date
 * @property array<string, string> $name
 * @property HolidayType $type
 * @property bool $nationwide
 * @property string $country_iso_code
 * @property string|null $subdivision_code
 * @property string|null $api_holiday_id
 * @property bool $is_custom
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
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

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'date' => 'date',
            'name' => 'array',
            'type' => HolidayType::class,
            'nationwide' => 'boolean',
            'country_iso_code' => 'string',
            'subdivision_code' => 'string',
            'api_holiday_id' => 'string',
            'is_custom' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
