<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

final readonly class PendingApprovalsQuery
{
    /**
     * @return Builder<Approval>
     */
    public function builder(Employee $manager): Builder
    {
        $directReportIds = $manager->directReports()->pluck('id');

        return Approval::query()
            ->where('status', RequestStatus::Pending)
            ->whereHasMorph('approvable', '*', function (Builder $query) use ($directReportIds): void {
                $query->whereIn('employee_id', $directReportIds);
            })
            ->with(['approvable'])->oldest();
    }
}
