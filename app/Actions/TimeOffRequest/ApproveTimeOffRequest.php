<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Enums\RequestStatus;
use App\Models\TimeOffRequest;

final readonly class ApproveTimeOffRequest
{
    public function handle(TimeOffRequest $timeOffRequest): TimeOffRequest
    {
        $timeOffRequest->update([
            'status' => RequestStatus::Approved,
        ]);

        return $timeOffRequest->refresh();
    }
}
