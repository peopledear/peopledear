<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PeopleDear\OfficeType;
use App\Models\Office;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Office::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', Rule::enum(OfficeType::class)],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'array'],
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['required', 'string', 'max:255'],
            'address.country' => ['required', 'string', 'max:255'],
        ];
    }
}
