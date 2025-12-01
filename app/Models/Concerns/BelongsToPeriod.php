<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Period;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPeriod
{
    /**
     * @return BelongsTo<Period, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
