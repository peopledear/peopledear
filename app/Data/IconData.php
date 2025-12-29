<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class IconData extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly string $name,
        public readonly string $icon,
        public readonly string $label
    ) {}

}
