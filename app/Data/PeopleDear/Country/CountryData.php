<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Country;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapOutputName(CamelCaseMapper::class)]
final class CountryData extends Data
{
    #[Computed]
    public string $displayName;

    /**
     * @param  array<string, string>  $name
     */
    public function __construct(
        public readonly int $id,
        public readonly string $isoCode,
        public readonly array $name,
    ) {
        $this->displayName = $this->resolveDisplayName();
    }

    private function resolveDisplayName(): string
    {
        if (isset($this->name['en'])) {
            return $this->name['en'];
        }

        if (isset($this->name['EN'])) {
            return $this->name['EN'];
        }

        $firstKey = array_key_first($this->name);
        if ($firstKey !== null) {
            return $this->name[$firstKey];
        }

        return $this->isoCode;
    }
}
