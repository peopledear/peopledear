<?php

declare(strict_types=1);

use App\Actions\Period\CreatePeriod;
use App\Enums\PeopleDear\PeriodStatus;
use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\UniqueConstraintViolationException;

beforeEach(function (): void {

    $this->action = app(CreatePeriod::class);

});

test('it closes periods',
    /**
     * @throws Throwable
     */
    function (): void {

        $organization = Organization::factory()
            ->createQuietly();

        $this->action->handle(2024, $organization);
        $this->action->handle(2025, $organization);

        $period2024 = Period::query()
            ->where('year', 2024)
            ->first();
        $period2025 = Period::query()
            ->where('year', 2025)
            ->first();

        expect($period2024->status)
            ->toBe(PeriodStatus::Closed)
            ->and($period2025->status)
            ->toBe(PeriodStatus::Active);
    });

test('throws and exception when period already exists',
    /**
     * @throws Throwable
     */
    function (): void {

        $organization = Organization::factory()
            ->createQuietly();

        $year = 2023;

        $this->action->handle($year, $organization);

        $this->action->handle($year, $organization);

    })->throws(UniqueConstraintViolationException::class);

test('it creates a period for the given year',
    /**
     * @throws Throwable
     */
    function (): void {

        $organization = Organization::factory()
            ->createQuietly();
        $year = 2023;

        $this->action->handle($year, $organization);

        $this->assertDatabaseHas('periods', [
            'year' => $year,
        ]);

    });
