<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CountrySubdivisionType;
use Database\Factories\CountrySubdivisionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $id
 * @property-read string $country_id
 * @property-read string|null $country_subdivision_id
 * @property-read array<string, string> $name
 * @property-read string $code
 * @property-read string $iso_code
 * @property-read string $short_name
 * @property-read CountrySubdivisionType $type
 * @property-read array<int, string> $official_languages
 * @property-read Country $country
 * @property-read CountrySubdivision|null $parent
 * @property-read Collection<int, CountrySubdivision> $children
 */
final class CountrySubdivision extends Model
{
    /** @use HasFactory<CountrySubdivisionFactory   > */
    use HasFactory;

    use HasUuids;

    public $timestamps = false;

    /** @return BelongsTo<Country, $this> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /** @return BelongsTo<CountrySubdivision, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'country_subdivision_id');
    }

    /** @return HasMany<CountrySubdivision, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'country_subdivision_id');
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'country_id' => 'string',
            'country_subdivision_id' => 'string',
            'type' => CountrySubdivisionType::class,
            'name' => 'array',
            'official_languages' => 'array',
        ];
    }
}
