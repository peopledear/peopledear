<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\PeopleDear\Holiday\CreateHolidayData;
use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 */
interface HolidayAdapter
{
    /**
     * @param  TData  $data
     */
    public function toCreateData(mixed $data, int $organizationId): CreateHolidayData;
}
