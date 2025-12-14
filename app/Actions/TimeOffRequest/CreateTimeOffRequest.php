<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Employee;
use App\Models\TimeOffRequest;
use App\Models\TimeOffType;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateTimeOffRequest
{
    /**
     * @throws Throwable
     */
    public function handle(
        CreateTimeOffRequestData $data,
        Employee $employee,
        TimeOffType $timeOffType
    ): TimeOffRequest {
        return DB::transaction(function () use ($data, $timeOffType): TimeOffRequest {
            /** @var TimeOffRequest $timeOffRequest */
            $timeOffRequest = TimeOffRequest::query()
                ->create([
                    ...$data->toArray(),
                    'status' => $timeOffType->requires_approval
                        ? RequestStatus::Pending
                        : RequestStatus::Approved,
                ]);

            return $timeOffRequest;
        });
    }
}
