<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Data;

final class OpenHolidaysSubdivisionReferenceData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $shortName,
    ) {}

}
