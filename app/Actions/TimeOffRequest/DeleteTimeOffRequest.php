<?php

declare(strict_types=1);

namespace App\Actions\TimeOffRequest;

use App\Models\TimeOffRequest;

final readonly class DeleteTimeOffRequest
{
    public function handle(TimeOffRequest $timeOff): void
    {
        $timeOff->delete();
    }
}
