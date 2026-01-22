<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Addressable;
use App\Enums\PeopleDear\LocationType;
use App\Models\Concerns\HasAddress;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Sprout\Attributes\TenantRelation;
use Sprout\Database\Eloquent\Concerns\BelongsToTenant;

/**
 * @property-read string $id
 * @property-read string $organization_id
 * @property-read string $country_id
 * @property-read string $name
 * @property-read LocationType $type
 * @property-read string|null $phone
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Country $country
 * @property-read Address $address
 * @property-read Collection<int, Employee> $employees
 */
final class Location extends Model implements Addressable
{
    use BelongsToTenant;
    use HasAddress;

    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    use HasUuids;

    public function casts(): array
    {
        return [
            'id' => 'string',
            'organization_id' => 'string',
            'country_id' => 'string',
            'name' => 'string',
            'type' => LocationType::class,
            'phone' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

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

    /** @return HasMany<Employee, $this> */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
