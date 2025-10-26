<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Data;

use Spatie\LaravelData\Data;

final class SubdivisionData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  array<int, array<string, mixed>>|null  $children
     */
    public function __construct(
        public readonly string $isoCode,
        public readonly string $shortName,
        public readonly array $name,
        public readonly ?string $officialLanguages = null,
        public readonly ?array $children = null,
    ) {}

    public function getLocalizedName(?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? config('openholidays.default_language', 'en');

        return $this->name[$languageCode] ?? $this->name['en'] ?? array_values($this->name)[0] ?? $this->shortName;
    }
}
