<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
final class UpdateOrganizationData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly string|Optional|null $vat_number,
        public readonly string|Optional|null $ssn,
        public readonly string|Optional|null $phone,
    ) {}
}
