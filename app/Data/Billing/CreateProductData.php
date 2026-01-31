<?php

declare(strict_types=1);

namespace App\Data\Billing;

use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateProductData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly bool $is_active,
    ) {}
}
