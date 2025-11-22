<?php

declare(strict_types=1);

namespace App\Actions\VacationBalance;

use App\Models\TimeOffRequest;
use App\Models\VacationBalance;

final readonly class RestoreVacationBalance
{
    public function __construct(
        private VacationBalance $vacationBalance,
    ) {}

    public function handle(TimeOffRequest $request): void
    {
        $amount = $this->calculateAmount($request);

        $this->vacationBalance->query()
            ->where('employee_id', $request->employee_id)
            ->where('year', $request->start_date->year)
            ->decrement('taken', $amount);
    }

    /**
     * Calculate amount in integer format (2.5 days = 250).
     */
    private function calculateAmount(TimeOffRequest $request): int
    {
        if ($request->is_half_day) {
            return 50; // 0.5 days = 50
        }

        $endDate = $request->end_date ?? $request->start_date;
        $days = (int) $request->start_date->startOfDay()->diffInDays($endDate->startOfDay()) + 1;

        return $days * 100; // Convert to integer format
    }
}
