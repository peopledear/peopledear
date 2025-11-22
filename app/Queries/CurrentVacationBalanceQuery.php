<?php

declare(strict_types=1);

namespace App\Queries;

use App\Attributes\CurrentEmployee;
use App\Models\Employee;
use App\Models\VacationBalance;
use Illuminate\Database\Eloquent\Builder;

final readonly class CurrentVacationBalanceQuery
{
    public function __construct(
        #[CurrentEmployee] private ?Employee $employee
    ) {}

    /**
     * @return Builder<VacationBalance>
     */
    public function builder(): Builder
    {
        return VacationBalance::query()
            ->where('year', now()->year)
            ->where('employee_id', $this->employee?->id);
    }
}
