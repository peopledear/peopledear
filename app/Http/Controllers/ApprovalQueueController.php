<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Approval\ApproveRequest;
use App\Actions\Approval\RejectRequest;
use App\Attributes\CurrentEmployee;
use App\Data\PeopleDear\Approval\RejectRequestData;
use App\Http\Requests\RejectRequestRequest;
use App\Models\Approval;
use App\Models\Employee;
use App\Queries\PendingApprovalsQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ApprovalQueueController
{
    public function index(
        #[CurrentEmployee] Employee $employee,
        PendingApprovalsQuery $pendingApprovalsQuery,
    ): Response {
        $pendingApprovals = $pendingApprovalsQuery
            ->builder($employee)
            ->with(['approvable.employee'])
            ->get();

        return Inertia::render('org-approvals/index', [
            'pendingApprovals' => $pendingApprovals,
        ]);
    }

    public function approve(
        Approval $approval,
        #[CurrentEmployee] Employee $employee,
        ApproveRequest $action,
    ): RedirectResponse {
        $action->handle($approval, $employee);

        return back();
    }

    public function reject(
        Approval $approval,
        RejectRequestRequest $request,
        #[CurrentEmployee] Employee $employee,
        RejectRequest $action,
    ): RedirectResponse {
        $data = RejectRequestData::from($request->validated());
        $action->handle($approval, $employee, $data);

        return back();
    }
}
