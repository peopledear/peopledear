<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpdateUserRoleData extends Data
{
    public function __construct(
        #[Required, Exists('roles', 'id')]
        public readonly int $role_id,
    ) {}
}
