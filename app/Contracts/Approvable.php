<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property-read ?Approval $approval
 *
 * @phpstan-require-extends Model
 */
interface Approvable
{
    /**
     * Get the approval relationship.
     *
     * @return MorphOne<Approval, covariant Model>
     */
    public function approval(): MorphOne;
}
