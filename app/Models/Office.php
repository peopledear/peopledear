<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OfficeType;
use Database\Factories\OfficeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property int $organization_id
 * @property string $name
 * @property OfficeType $type
 * @property string|null $phone
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Organization $organization
 * @property-read Address $address
 */
final class Office extends Model
{
    /** @use HasFactory<OfficeFactory> */
    use HasFactory;

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return MorphOne<Address, $this> */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function casts(): array
    {
        return [
            'id' => 'integer',
            'organization_id' => 'integer',
            'name' => 'string',
            'type' => OfficeType::class,
            'phone' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
