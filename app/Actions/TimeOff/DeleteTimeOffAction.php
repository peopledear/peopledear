<?php

declare(strict_types=1);

namespace App\Actions\TimeOff;

use App\Models\TimeOff;

final readonly class DeleteTimeOffAction
{
    public function handle(TimeOff $timeOff): void
    {
        $timeOff->delete();
    }
}
