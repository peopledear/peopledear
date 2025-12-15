<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\TimeOffRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class TimeOffRequestQuery
{
    /** @var Builder<TimeOffRequest> */
    private Builder $builder;

    public function __invoke(?string $id = null): self
    {
        $this->builder = TimeOffRequest::query();

        if ($id) {
            $this->builder->where('id', $id);
        }

        return $this;
    }

    /** @return Builder<TimeOffRequest> */
    public function make(): Builder
    {
        return $this->builder;
    }

    /** @return Collection<int, TimeOffRequest> */
    public function get(): Collection
    {
        return $this->builder->get();
    }

    public function first(): ?TimeOffRequest
    {
        return $this->builder->first();
    }

    /**
     * @return LengthAwarePaginator<int, TimeOffRequest>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->builder->paginate($perPage);
    }

    /**
     * @param  string[]  $relations
     */
    public function withRelations(array $relations = []): self
    {

        if ($relations === []) {
            $relations = ['employee', 'organization', 'period', 'type'];
        }

        $this->builder->with($relations);

        return $this;
    }

    public function ofEmployee(string $employeeId): self
    {
        $this->builder->where('employee_id', $employeeId);

        return $this;
    }

    public function latest(int $count = 5): self
    {
        $this->builder
            ->limit($count)
            ->latest();

        return $this;
    }

    public function ofStatus(RequestStatus $status): self
    {
        $this->builder->where('status', $status->value);

        return $this;
    }

    public function ofType(string $timeOffTypeId): self
    {
        $this->builder->where('time_off_type_id', $timeOffTypeId);

        return $this;
    }

    /**
     * @param  array<int, RequestStatus>  $statuses
     */
    public function statusIn(array $statuses): self
    {
        $this->builder->whereIn('status', array_map(fn (RequestStatus $status) => $status->value, $statuses));

        return $this;
    }

    public function pendingApproval(): self
    {
        $this->builder->where('status', RequestStatus::Pending);

        return $this;
    }

    public function approved(): self
    {
        $this->builder->where('status', RequestStatus::Approved);

        return $this;
    }

    public function rejected(): self
    {
        $this->builder->where('status', RequestStatus::Rejected);

        return $this;
    }

    public function cancelled(): self
    {
        $this->builder->where('status', RequestStatus::Cancelled);

        return $this;
    }
}
