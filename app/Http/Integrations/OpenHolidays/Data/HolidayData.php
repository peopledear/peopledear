<?php

declare(strict_types=1);

namespace App\Http\Integrations\OpenHolidays\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

final class HolidayData extends Data
{
    /**
     * @param  array<string, string>  $name
     * @param  array<int, string>  $type
     * @param  array<int, array<string, mixed>>|null  $subdivisions
     */
    public function __construct(
        public readonly string $id,
        public readonly CarbonImmutable $startDate,
        public readonly CarbonImmutable $endDate,
        public readonly array $name,
        public readonly bool $nationwide,
        public readonly array $type,
        public readonly ?array $subdivisions = null,
        public readonly ?string $comment = null,
    ) {}

    public function isIncludedType(): bool
    {
        $includedTypes = config('openholidays.included_types', []);

        foreach ($this->type as $type) {
            if (in_array($type, $includedTypes, true)) {
                return true;
            }
        }

        return false;
    }

    public function getLocalizedName(?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? config('openholidays.default_language', 'en');

        return $this->name[$languageCode] ?? $this->name['en'] ?? array_values($this->name)[0] ?? 'Unknown Holiday';
    }
}
