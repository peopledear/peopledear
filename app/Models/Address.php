<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $addressable_id
 * @property-read string $addressable_type
 * @property-read string $line1
 * @property-read string|null $line2
 * @property-read string $city
 * @property-read string|null $state
 * @property-read string $postal_code
 * @property-read string $country
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Model $addressable
 */
final class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    use HasUuids;

    /** @return MorphTo<Model, $this> */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'addressable_id' => 'string',
            'addressable_type' => 'string',
            'line1' => 'string',
            'line2' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postal_code' => 'string',
            'country' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
