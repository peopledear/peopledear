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

    private ?int $type = null;

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

        if ($this->type !== null) {
            $query->where('type', $this->type);
        }

        return $query;
    }

    public function withStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
