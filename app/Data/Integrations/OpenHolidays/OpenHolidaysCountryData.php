<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Data;

final class OpenHolidaysCountryData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  array<int, string>  $officialLanguages
     */
    public function __construct(
        public readonly string $isoCode,
        public readonly array $name,
        public readonly array $officialLanguages,
    ) {}

    public function getLocalizedName(?string $languageCode = null): string
    {
        $languageCode ??= config()->string('openholidays.default_language', 'en');

        $values = array_values($this->name);

        return $this->name[$languageCode] ?? $this->name['en'] ?? $values[0] ?? $this->isoCode;
    }
}
