<?php

declare(strict_types=1);

namespace App\Validators\TimeOffType;

use App\Contracts\TimeOffTypeValidator;
use App\Models\VacationBalance;
use App\Support\ValidationResult;

final readonly class VacationValidator implements TimeOffTypeValidator
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        /** @var string $startDateValue */
        $startDateValue = $data['start_date'];
        $startDate = \Illuminate\Support\Facades\Date::parse($startDateValue);

        $endDate = null;
        if (isset($data['end_date'])) {
            /** @var string $endDateValue */
            $endDateValue = $data['end_date'];
            $endDate = \Illuminate\Support\Facades\Date::parse($endDateValue);
        }

        if ($endDate !== null && $endDate->startOfDay()->lt($startDate->startOfDay())) {
            $errors['end_date'] = 'End date must be on or after start date.';
        }

        if ($errors === []) {
            $requestedDays = $this->calculateRequestedDays($data);
            /** @var int $employeeId */
            $employeeId = $data['employee_id'];
            $availableBalance = $this->getAvailableBalance($employeeId);

            // Convert to display format (divide by 100)
            $availableDaysDisplay = $availableBalance / 100;
            $requestedDaysDisplay = $requestedDays / 100;

            if ($requestedDays > $availableBalance) {
                $errors['balance'] = sprintf('Insufficient vacation balance. You have %s days available but requested %s days.', $availableDaysDisplay, $requestedDaysDisplay);
            }
        }

        if ($errors !== []) {
            return ValidationResult::fail($errors);
        }

        return ValidationResult::pass();
    }

    /**
     * Calculate requested days in integer format (2.5 days = 250).
     *
     * @param  array<string, mixed>  $data
     */
    private function calculateRequestedDays(array $data): int
    {
        if (isset($data['is_half_day']) && $data['is_half_day']) {
            return 50; // 0.5 days = 50
        }

        /** @var string $startDateValue */
        $startDateValue = $data['start_date'];
        $startDate = \Illuminate\Support\Facades\Date::parse($startDateValue);

        $endDate = $startDate;
        if (isset($data['end_date'])) {
            /** @var string $endDateValue */
            $endDateValue = $data['end_date'];
            $endDate = \Illuminate\Support\Facades\Date::parse($endDateValue);
        }

        $days = (int) $startDate->startOfDay()->diffInDays($endDate->startOfDay()) + 1;

        return $days * 100; // Convert to integer format
    }

    private function getAvailableBalance(int $employeeId): int
    {
        /** @var VacationBalance|null $balance */
        $balance = VacationBalance::query()
            ->withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->where('year', now()->year)
            ->first();

        return $balance->remaining ?? 0;
    }
}
