<?php

declare(strict_types=1);

namespace App\Contracts;

use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 * @template TCreateData of Data
 *
 * @extends Adapter<TData, TCreateData>
 */
interface CountrySubdivisionAdapter extends Adapter {}
