<?php

declare(strict_types=1);

namespace App\Data\PeopleDear\Approval;

use Spatie\LaravelData\Data;

final class RejectRequestData extends Data
{
    public function __construct(
        public readonly string $rejection_reason,
    ) {}
}
