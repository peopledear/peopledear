<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Data\PeopleDear\TimeOffRequest\UpdateTimeOffRequestData;
use App\Models\TimeOffRequest;
use Spatie\LaravelData\Optional;

final readonly class UpdateTimeOffRequest
{
    public function handle(UpdateTimeOffRequestData $data, TimeOffRequest $timeOff): TimeOffRequest
    {
        $updates = [];

        if (! $data->type instanceof Optional) {
            $updates['type'] = $data->type;
        }

        if (! $data->status instanceof Optional) {
            $updates['status'] = $data->status;
        }

        if (! $data->startDate instanceof Optional) {
            $updates['start_date'] = $data->startDate;
        }

        if (! $data->endDate instanceof Optional) {
            $updates['end_date'] = $data->endDate;
        }

        if (! $data->isHalfDay instanceof Optional) {
            $updates['is_half_day'] = $data->isHalfDay;
        }

        if ($updates !== []) {
            $timeOff->update($updates);
        }

        return $timeOff->refresh();
    }
}
