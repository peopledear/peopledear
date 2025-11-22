<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToEmployee
{
    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
