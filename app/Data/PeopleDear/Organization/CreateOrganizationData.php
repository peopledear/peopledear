<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Organization;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateOrganizationData extends Data
{
    public function __construct(
        public readonly string $name,
        #[MapInputName('country_id')]
        #[MapOutputName('country_id')]
        public readonly string $countryId,
    ) {}
}
