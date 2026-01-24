<?php

declare(strict_types=1);

use App\Actions\Period\ClosePeriods;
use App\Enums\PeriodStatus;
use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Sequence;

beforeEach(function (): void {
    $this->action = resolve(ClosePeriods::class);
});

test('closes periods',
    /**
     * @throws Throwable
     */
    function (): void {

        $organization = Organization::factory()
            ->create();

        $periods = Period::factory()->count(3)
            ->for($organization)
            ->state(new Sequence(
                ['year' => 2022],
                ['year' => 2023],
                ['year' => 2024]
            ))
            ->create([
                'status' => 1, // Active
            ])
            ->fresh();

        $this->action->handle(2025, $organization);

        foreach ($periods as $period) {
            expect($period->fresh()->status)
                ->toBe(PeriodStatus::Closed);
        }

    });
