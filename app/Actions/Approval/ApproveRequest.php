<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\TimeOffRequest;
use App\Registries\TimeOffTypeRegistry;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class ApproveRequest
{
    public function __construct(
        private TimeOffTypeRegistry $registry,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Approval $approval, Employee $approver): Approval
    {
        return DB::transaction(function () use ($approval, $approver): Approval {
            $approval->update([
                'status' => RequestStatus::Approved,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            $approvable = $approval->approvable;

            if ($approvable instanceof TimeOffRequest) {
                $processor = $this->registry->getProcessor($approvable->type);
                $processor->process($approvable);
            }

            return $approval->refresh();
        });
    }
}
