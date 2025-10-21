<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class UpdateUserRoleData extends Data
{
    public function __construct(
        public readonly int $role_id,
    ) {}

    /**
     * @return array<string,array<string>>
     */
    public static function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
