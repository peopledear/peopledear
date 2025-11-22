<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Data\PeopleDear\TimeOffRequest\CreateTimeOffRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\TimeOffType;
use App\Models\TimeOffRequest;

final readonly class CreateTimeOffRequest
{
    public function handle(CreateTimeOffRequestData $data): TimeOffRequest
    {
        /** @var TimeOffRequest $timeOff */
        $timeOff = TimeOffRequest::query()->create([
            'organization_id' => $data->organization_id,
            'employee_id' => $data->employee_id,
            'type' => $data->type,
            'status' => RequestStatus::Pending,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_half_day' => $data->is_half_day,
        ]);

        $isAutoApproved = $data->type === TimeOffType::SickLeave;

        $timeOff->approval()->create([
            'organization_id' => $data->organization_id,
            'status' => $isAutoApproved ? RequestStatus::Approved : RequestStatus::Pending,
            'approved_at' => $isAutoApproved ? now() : null,
        ]);

        return $timeOff;
    }
}
