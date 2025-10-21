<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class ResendInvitationData extends Data
{
    public function __construct(
        public readonly string $email,
    ) {}

    /**
     * @return array<string,array<string>>
     */
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
