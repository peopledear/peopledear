<?php

declare(strict_types=1);

namespace App\Actions\TimeOff;

use App\Data\CreateTimeOffData;
use App\Enums\PeopleDear\TimeOffStatus;
use App\Models\TimeOff;

final readonly class CreateTimeOffAction
{
    public function handle(CreateTimeOffData $data): TimeOff
    {
        /** @var TimeOff $timeOff */
        $timeOff = TimeOff::query()->create([
            'organization_id' => $data->organization_id,
            'employee_id' => $data->employee_id,
            'type' => $data->type,
            'status' => TimeOffStatus::Pending,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_half_day' => $data->is_half_day,
        ]);

        return $timeOff;
    }
}
