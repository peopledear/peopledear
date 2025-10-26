<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property string $name
 * @property string|null $vat_number
 * @property string|null $ssn
 * @property string|null $phone
 * @property string|null $country_iso_code
 * @property string|null $subdivision_code
 * @property string|null $language_iso_code
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Collection<int, Office> $offices
 * @property-read Collection<int, Holiday> $holidays
 */
final class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    /** @return HasMany<Office, $this> */
    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    /** @return HasMany<Holiday, $this> */
    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'vat_number' => 'string',
            'ssn' => 'string',
            'phone' => 'string',
            'country_iso_code' => 'string',
            'subdivision_code' => 'string',
            'language_iso_code' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
