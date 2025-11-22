<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasApproval
{
    /**
     * Get the approval relationship.
     *
     * @return MorphOne<Approval, $this>
     */
    public function approval(): MorphOne
    {
        return $this->morphOne(Approval::class, 'approvable');
    }
}
