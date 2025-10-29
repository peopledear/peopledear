<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $iso_code
 * @property-read array<string, string> $name
 * @property-read array<int, string> $official_languages
 * @property-read Collection<int, Organization> $organizations
 * @property-read Collection<int, CountrySubdivision> $subdivisions
 */
final class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @return HasMany<Organization, $this> */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /** @return HasMany<CountrySubdivision, $this> */
    public function subdivisions(): HasMany
    {
        return $this->hasMany(CountrySubdivision::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'array',
            'official_languages' => 'array',
        ];
    }
}
