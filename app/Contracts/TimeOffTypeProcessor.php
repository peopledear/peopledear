<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\TimeOffRequest;

interface TimeOffTypeProcessor
{
    /**
     * Process an approved time-off request.
     */
    public function process(TimeOffRequest $request): void;

    /**
     * Reverse the effects of processing (for cancellation).
     */
    public function reverse(TimeOffRequest $request): void;
}
