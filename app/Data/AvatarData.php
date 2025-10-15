<?php

declare(strict_types=1);

namespace App\Data;

final readonly class AvatarData
{
    public function __construct(
        public ?string $path,
        public ?string $src,
        public ?string $alt,
    ) {}

}
