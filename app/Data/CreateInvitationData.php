<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class CreateInvitationData extends Data
{
    public function __construct(
        #[Required, Email, Max(255)]
        public readonly string $email,
        #[Required, Exists('roles', 'id')]
        public readonly int $role_id,
    ) {}
}
