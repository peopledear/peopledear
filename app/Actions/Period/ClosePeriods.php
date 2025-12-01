<?php

declare(strict_types=1);

namespace App\Actions\Period;

use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Organization;
use App\Models\Period;

final class ClosePeriods
{
    public function handle(int $year, Organization $organization): void
    {
        Period::query()
            ->whereNot('year', $year)
            ->whereNot('status', PeriodStatus::Closed)
            ->where('organization_id', $organization->id)
            ->update(['status' => PeriodStatus::Closed]);
    }
}
