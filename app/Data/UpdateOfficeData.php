<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\OfficeType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * @method array<string, mixed> toArray()
 */
final class UpdateOfficeData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly OfficeType|Optional $type,
        public readonly string|Optional|null $phone,
        public readonly UpdateAddressData|Optional $address,
    ) {}
}
