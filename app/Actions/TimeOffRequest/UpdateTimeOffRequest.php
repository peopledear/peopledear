<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Data\PeopleDear\TimeOffRequest\UpdateTimeOffRequestData;
use App\Models\TimeOffRequest;

final readonly class UpdateTimeOffRequest
{
    public function handle(
        TimeOffRequest $timeOff,
        UpdateTimeOffRequestData $attributes
    ): TimeOffRequest {

        $timeOff->update($attributes->toArray());

        return $timeOff->refresh();
    }
}
