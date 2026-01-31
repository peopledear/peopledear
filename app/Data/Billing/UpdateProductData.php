<?php

declare(strict_types=1);

namespace App\Data\Billing;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
final class UpdateProductData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly string|Optional|null $description,
        public readonly bool|Optional $is_active,
    ) {}
}
