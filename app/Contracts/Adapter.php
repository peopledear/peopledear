<?php

declare(strict_types=1);

namespace App\Contracts;

use Spatie\LaravelData\Data;

/**
 * Generic adapter interface for transforming external data to internal Data objects.
 *
 * This interface provides a flexible pattern for adapting external API data
 * to internal Data transfer objects with contextual information passed as arrays.
 *
 * @template TData of Data
 * @template TCreateData of Data
 */
interface Adapter
{
    /**
     * Transform external data to an internal CreateData object.
     *
     * @param  TData  $data  The external data object to transform
     * @return TCreateData The transformed internal data object
     */
    public function toCreateData(mixed $data): mixed;
}
