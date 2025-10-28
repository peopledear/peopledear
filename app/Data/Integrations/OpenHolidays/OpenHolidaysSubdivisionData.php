<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Data;

final class OpenHolidaysSubdivisionData extends Data
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
        $languageCode ??= config()->string('openholidays.default_language', 'en');

        $values = array_values($this->name);

        return $this->name[$languageCode] ?? $this->name['en'] ?? $values[0] ?? $this->shortName;
    }
}
