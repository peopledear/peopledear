<?php

declare(strict_types=1);

namespace App\Validators\TimeOffType;

use App\Contracts\TimeOffTypeValidator;
use App\Support\ValidationResult;
use Illuminate\Support\Facades\Date;

final readonly class PersonalDayValidator implements TimeOffTypeValidator
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        /** @var string $startDateValue */
        $startDateValue = $data['start_date'];
        $startDate = Date::parse($startDateValue);

        $endDate = null;
        if (isset($data['end_date'])) {
            /** @var string $endDateValue */
            $endDateValue = $data['end_date'];
            $endDate = Date::parse($endDateValue);
        }

        if ($endDate !== null && $endDate->startOfDay()->lt($startDate->startOfDay())) {
            $errors['end_date'] = 'End date must be on or after start date.';
        }

        if ($errors !== []) {
            return ValidationResult::fail($errors);
        }

        return ValidationResult::pass();
    }
}
