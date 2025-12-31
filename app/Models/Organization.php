<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read string $slug
 * @property-read string|null $vat_number
 * @property-read string|null $ssn
 * @property-read string|null $phone
 * @property-read int|null $country_id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Country|null $country
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Office> $offices
 * @property-read Collection<int, Holiday> $holidays
 */
final class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    use HasUuids;

    public function casts(): array
    {
        return [
            'id' => 'string',
            'name' => 'string',
            'slug' => 'string',
            'vat_number' => 'string',
            'ssn' => 'string',
            'phone' => 'string',
            'country_id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /** @return BelongsTo<Country, $this> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

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

    /** @return HasMany<Period, $this> */
    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    /** @return HasMany<TimeOffType, $this> */
    public function timeOffTypes(): HasMany
    {
        return $this->hasMany(TimeOffType::class);
    }

    public function hasLocationConfigured(): bool
    {
        return $this->country_id !== null;
    }
}
