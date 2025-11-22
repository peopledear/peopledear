<?php

declare(strict_types=1);

namespace App\Support;

final readonly class ValidationResult
{
    /**
     * @param  array<string, string>  $errors
     */
    public function __construct(
        public bool $valid,
        public array $errors = [],
    ) {}

    public static function pass(): self
    {
        return new self(valid: true, errors: []);
    }

    /**
     * @param  array<string, string>  $errors
     */
    public static function fail(array $errors): self
    {
        return new self(valid: false, errors: $errors);
    }
}
