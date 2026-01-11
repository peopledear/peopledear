<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PeopleDear\LocationType;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Sprout\Contracts\Tenant;
use Sprout\Contracts\TenantHasResources;
use Sprout\Database\Eloquent\Concerns\HasTenantResources;
use Sprout\Database\Eloquent\Concerns\IsTenant;

/**
 * @property-read string $id
 * @property-read string $identifier
 * @property-read string $resource_key
 * @property-read string $name
 * @property-read string $slug
 * @property-read string|null $vat_number
 * @property-read string|null $ssn
 * @property-read string|null $phone
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Country|null $country
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Location> $locations
 * @property-read Collection<int, Location> $headquarters
 * @property-read Collection<int, Holiday> $holidays
 */
final class Organization extends Model implements Tenant, TenantHasResources
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    use HasTenantResources;
    use HasUuids;
    use IsTenant;

    public function casts(): array
    {
        return [
            'id' => 'string',
            'name' => 'string',
            'identifier' => 'string',
            'resource_key' => 'string',
            'vat_number' => 'string',
            'ssn' => 'string',
            'phone' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Location, $this> */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /** @return HasMany<Location, $this> */
    public function headquarters(): HasMany
    {
        return $this->hasMany(Location::class)
            ->where('type', LocationType::Headquarters->value);
    }

    /** @return HasOne<Location, $this> */
    public function headOffice(): HasOne
    {
        return $this->hasOne(Location::class)
            ->where('type', LocationType::Headquarters->value)
            ->latest();
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
        return $this->headOffice()->exists();
    }
}
