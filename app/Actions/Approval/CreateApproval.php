<?php

declare(strict_types=1);

namespace App\Actions\Approval;

use App\Contracts\Approvable;
use App\Enums\PeopleDear\RequestStatus;
use App\Models\Approval;

final readonly class CreateApproval
{
    public function handle(
        Approvable $approvable,
        string $organizationId,
        RequestStatus $status = RequestStatus::Pending,
    ): Approval {
        /** @var Approval $approval */
        $approval = Approval::query()->create([
            'organization_id' => $organizationId,
            'approvable_type' => $approvable::class,
            'approvable_id' => $approvable->getKey(),
            'status' => $status,
            'approved_at' => $status === RequestStatus::Approved ? now() : null,
        ]);

        return $approval;
    }
}
