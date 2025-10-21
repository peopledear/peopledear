<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

final class CreateInvitationData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly int $role_id,
    ) {}

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->whereNull('accepted_at'),
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered or has a pending invitation.',
        ];
    }
}
