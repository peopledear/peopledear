<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Data;

final class OpenHolidaysLocalizedTextData extends Data
{
    public function __construct(
        public readonly string $language,
        public readonly string $text,
    ) {}

}
