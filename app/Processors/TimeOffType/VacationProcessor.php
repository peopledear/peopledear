<?php

declare(strict_types=1);

namespace App\Processors\TimeOffType;

use App\Actions\TimeOffRequest\ApproveTimeOffRequest;
use App\Actions\TimeOffRequest\CancelTimeOffRequest;
use App\Actions\VacationBalance\DeductVacationBalance;
use App\Actions\VacationBalance\RestoreVacationBalance;
use App\Contracts\TimeOffTypeProcessor;
use App\Models\TimeOffRequest;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class VacationProcessor implements TimeOffTypeProcessor
{
    public function __construct(
        private ApproveTimeOffRequest $approveTimeOffRequest,
        private CancelTimeOffRequest $cancelTimeOffRequest,
        private DeductVacationBalance $deductVacationBalance,
        private RestoreVacationBalance $restoreVacationBalance,
    ) {}

    /**
     * @throws Throwable
     */
    public function process(TimeOffRequest $request): void
    {
        DB::transaction(function () use ($request): void {
            $this->approveTimeOffRequest->handle($request);
            $this->deductVacationBalance->handle($request);
        });
    }

    /**
     * @throws Throwable
     */
    public function reverse(TimeOffRequest $request): void
    {
        DB::transaction(function () use ($request): void {
            $this->cancelTimeOffRequest->handle($request);
            $this->restoreVacationBalance->handle($request);
        });
    }
}
