<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PeopleDear\LocationType;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read Location $location
 */
final class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->location) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', Rule::enum(LocationType::class)],
            'country_id' => ['required', 'string', 'exists:countries,id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'array'],
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['required', 'string', 'max:50'],
            'address.country' => ['required', 'string', 'max:255'],
        ];
    }
}
