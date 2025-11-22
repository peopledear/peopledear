<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;

final readonly class CancelRequest
{
    public function handle(Approval $approval): Approval
    {
        $approval->update([
            'status' => RequestStatus::Cancelled,
        ]);

        return $approval->refresh();
    }
}
