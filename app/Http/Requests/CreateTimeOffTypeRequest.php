<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\TimeOffUnit;
use App\Models\TimeOffType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateTimeOffTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', TimeOffType::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allowed_units' => ['required', 'array', 'min:0', Rule::in(TimeOffUnit::cases())],
            'icon' => ['required', 'string', 'max:255', Rule::in(Icon::cases())],
            'color' => ['required', 'string', 'min:7', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'requires_approval' => ['required', 'boolean'],
            'requires_justification' => ['required', 'boolean'],
            'requires_justification_document' => ['required', 'boolean'],

            'balance_mode' => ['required', 'integer', Rule::in(BalanceType::cases())],
        ];
    }
}
