<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read Organization $organization
 */
final class UpdateOrganizationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:255'],
            'ssn' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
