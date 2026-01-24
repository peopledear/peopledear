<?php

declare(strict_types=1);

namespace App\Actions\Period;

use App\Enums\PeriodStatus;
use App\Models\Organization;
use App\Models\Period;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePeriod
{
    public function __construct(
        private ClosePeriods $closePeriods,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(int $year, Organization $organization): void
    {
        DB::transaction(function () use ($year, $organization): void {

            $this->closePeriods->handle($year, $organization);

            $date = new CarbonImmutable($year);

            Period::query()->create([
                'organization_id' => $organization->id,
                'year' => $year,
                'start' => $date->startOfYear(),
                'end' => $date->endOfYear(),
                'status' => PeriodStatus::Active,
            ]);

        });
    }
}
