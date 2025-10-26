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
        /** @var array<int, string> $includedTypes */
        $includedTypes = config()->array('openholidays.included_types', []);

        return array_any($this->type, fn (string $type): bool => in_array($type, $includedTypes, true));
    }

    public function getLocalizedName(?string $languageCode = null): string
    {
        $languageCode ??= config()->string('openholidays.default_language', 'en');

        $values = array_values($this->name);

        return $this->name[$languageCode] ?? $this->name['en'] ?? $values[0] ?? 'Unknown Holiday';
    }
}
