<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Data;

use Spatie\LaravelData\Data;

final class CountryData extends Data
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
        $languageCode = $languageCode ?? config('openholidays.default_language', 'en');

        return $this->name[$languageCode] ?? $this->name['en'] ?? array_values($this->name)[0] ?? $this->isoCode;
    }
}
