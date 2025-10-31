<?php

declare(strict_types=1);

namespace App\Data\Integrations\OpenHolidays;

use Spatie\LaravelData\Data;

final class OpenHolidaysSubdivisionData extends Data
{
    /**
     * @param  array<int, array<string, string>>  $category
     * @param  array<int, array<string, string>>  $name
     * @param  array<int, string>|null  $officialLanguages
     * @param  array<int, array<string, mixed>>|null  $children
     */
    public function __construct(
        public readonly string $code,
        public readonly string $isoCode,
        public readonly string $shortName,
        public readonly array $category,
        public readonly array $name,
        public readonly ?array $officialLanguages = null,
        public readonly ?array $children = null,
    ) {}

    public function getLocalizedName(?string $languageCode = null): string
    {
        $languageCode ??= config()->string('openholidays.default_language', 'en');

        $nameMap = [];

        foreach ($this->name as $item) {
            $nameMap[$item['language']] = $item['text'];
        }

        return $nameMap[$languageCode] ?? $nameMap['en'] ?? $nameMap[array_key_first($nameMap)] ?? $this->shortName;
    }
}
