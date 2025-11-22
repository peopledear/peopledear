<?php

declare(strict_types=1);

namespace App\Processors\TimeOffType;

use App\Actions\TimeOffRequest\ApproveTimeOffRequest;
use App\Actions\TimeOffRequest\CancelTimeOffRequest;
use App\Contracts\TimeOffTypeProcessor;
use App\Models\TimeOffRequest;

final readonly class PersonalDayProcessor implements TimeOffTypeProcessor
{
    public function __construct(
        private ApproveTimeOffRequest $approveTimeOffRequest,
        private CancelTimeOffRequest $cancelTimeOffRequest,
    ) {}

    public function process(TimeOffRequest $request): void
    {
        $this->approveTimeOffRequest->handle($request);
    }

    public function reverse(TimeOffRequest $request): void
    {
        $this->cancelTimeOffRequest->handle($request);
    }
}
