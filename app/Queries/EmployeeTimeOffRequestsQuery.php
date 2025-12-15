<?php

declare(strict_types=1);

namespace App\Queries;

use App\Attributes\CurrentEmployee;
use App\Models\Employee;
use App\Models\TimeOffRequest;
use Illuminate\Database\Eloquent\Builder;

final class EmployeeTimeOffRequestsQuery
{
    private ?int $status = null;

    private ?string $timeOffTypeId = null;

    public function __construct(
        #[CurrentEmployee] private readonly ?Employee $employee,
    ) {}

    /**
     * @return Builder<TimeOffRequest>
     */
    public function builder(): Builder
    {
        $query = TimeOffRequest::query()
            ->where('employee_id', $this->employee?->id)
            ->latest('created_at');

        if ($this->status !== null) {
            $query->where('status', $this->status);
        }

        if ($this->timeOffTypeId !== null) {
            $query->where('time_off_type_id', $this->timeOffTypeId);
        }

        return $query;
    }

    public function withStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withType(?string $timeOffTypeId): self
    {
        $this->timeOffTypeId = $timeOffTypeId;

        return $this;
    }
}
