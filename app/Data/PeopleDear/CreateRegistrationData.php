<?php

declare(strict_types=1);

namespace App\Data\PeopleDear;

use SensitiveParameter;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @method array<string, mixed> toArray()
 */
#[MapName(SnakeCaseMapper::class)]
final class CreateRegistrationData extends Data
{
    public function __construct(
        public readonly string $organizationName,
        public readonly string $name,
        public readonly string $email,
        #[SensitiveParameter]
        public readonly string $password,
    ) {}

}
