<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Support\ValidationResult;

interface TimeOffTypeValidator
{
    /**
     * Validate the time-off request data.
     *
     * @param  array<string, mixed>  $data
     */
    public function validate(array $data): ValidationResult;
}
