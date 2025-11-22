<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;

final readonly class ApproveRequest
{
    public function handle(Approval $approval, Employee $approver): Approval
    {
        $approval->update([
            'status' => RequestStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $approval->refresh();
    }
}
