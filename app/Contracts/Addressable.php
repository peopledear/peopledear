<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property-read ?Address $address
 *
 * @phpstan-require-extends Model
 */
interface Addressable
{
    /**
     * Get the address relationship.
     *
     * @return MorphOne<Address, covariant Model>
     */
    public function address(): MorphOne;
}
