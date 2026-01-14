<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use function app;

final class CreateRegistrationRequest extends FormRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_name' => ['required', 'string', 'max:255', 'min:3'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                'email',
                new ValidEmail,
                Rule::unique(User::class)
                    ->where('organization_id', null),
            ],
            'password' => [
                'required',
                'confirmed',
                ...$this->getPasswordRule(),
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private function getPasswordRule(): array
    {

        return app()->environment('local')
            ? []
            : [Password::defaults()];
    }
}
