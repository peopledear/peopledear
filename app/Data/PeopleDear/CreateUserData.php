<?php

declare(strict_types=1);

namespace App\Data\PeopleDear;

use SensitiveParameter;
use Spatie\LaravelData\Data;

/**
 * @method array<string, mixed> toArray()
 */
final class CreateUserData extends Data
{
    public function __construct(
        public readonly string $organization_id,
        public readonly string $name,
        public readonly string $email,
        #[SensitiveParameter]
        public readonly string $password,
    ) {}
}
