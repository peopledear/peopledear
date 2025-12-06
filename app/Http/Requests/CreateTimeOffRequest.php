<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PeopleDear\TimeOffType;
use App\Registries\TimeOffTypeRegistry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateTimeOffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): true
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'period_id' => ['required', 'string', 'exists:periods,id'],
            'type' => ['required', Rule::enum(TimeOffType::class)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_half_day' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(TimeOffTypeRegistry $registry): array
    {
        return [
            function (Validator $validator) use ($registry): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $validated = $this->validated();
                /** @var int|string $typeValue */
                $typeValue = $this->input('type');
                $type = TimeOffType::from((int) $typeValue);

                $typeValidator = $registry->getValidator($type);
                $result = $typeValidator->validate($validated);

                if (! $result->valid) {
                    foreach ($result->errors as $field => $message) {
                        $validator->errors()->add($field, $message);
                    }
                }
            },
        ];
    }
}
