<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Data\PeopleDear\Approval\RejectRequestData;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;

final readonly class RejectRequest
{
    public function handle(Approval $approval, Employee $approver, RejectRequestData $data): Approval
    {
        $approval->update([
            'status' => RequestStatus::Rejected,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $data->rejection_reason,
        ]);

        return $approval->refresh();
    }
}
