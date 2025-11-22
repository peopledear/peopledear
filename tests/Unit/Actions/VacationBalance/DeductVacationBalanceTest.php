<?php

declare(strict_types=1);

use App\Actions\VacationBalance\DeductVacationBalance;
use App\Models\TimeOffRequest;
use App\Models\VacationBalance;

test('deducts single day from balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->createQuietly([
            'start_date' => now(),
            'end_date' => null,
            'is_half_day' => false,
        ]);

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->createQuietly([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 0,
    ]);

    $action = app(DeductVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(100);
});

test('deducts multi-day from balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->createQuietly([
            'start_date' => now(),
            'end_date' => now()->addDays(4),
            'is_half_day' => false,
        ]);

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->createQuietly([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 0,
    ]);

    $action = app(DeductVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(500);
});

test('deducts half day from balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->halfDay()
        ->createQuietly();

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->createQuietly([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 0,
    ]);

    $action = app(DeductVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(50);
});
