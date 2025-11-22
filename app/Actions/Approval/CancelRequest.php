<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;
use App\Models\TimeOffRequest;
use App\Registries\TimeOffTypeRegistry;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CancelRequest
{
    public function __construct(
        private TimeOffTypeRegistry $registry,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(Approval $approval): Approval
    {
        return DB::transaction(function () use ($approval): Approval {
            $wasApproved = $approval->status === RequestStatus::Approved;

            $approval->update([
                'status' => RequestStatus::Cancelled,
            ]);

            $approvable = $approval->approvable;

            if ($wasApproved && $approvable instanceof TimeOffRequest) {
                $processor = $this->registry->getProcessor($approvable->type);
                $processor->reverse($approvable);
            }

            return $approval->refresh();
        });
    }
}
