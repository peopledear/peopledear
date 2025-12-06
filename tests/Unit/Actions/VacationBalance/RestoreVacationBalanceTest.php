<?php

declare(strict_types=1);

use App\Actions\VacationBalance\RestoreVacationBalance;
use App\Models\TimeOffRequest;
use App\Models\VacationBalance;

test('restores single day to balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->create([
            'start_date' => now(),
            'end_date' => null,
            'is_half_day' => false,
        ]);

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->create([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 100,
    ]);

    $action = app(RestoreVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(0);
});

test('restores multi-day to balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->create([
            'start_date' => now(),
            'end_date' => now()->addDays(4),
            'is_half_day' => false,
        ]);

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->create([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 500,
    ]);

    $action = app(RestoreVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(0);
});

test('restores half day to balance', function (): void {
    /** @var TimeOffRequest $request */
    $request = TimeOffRequest::factory()
        ->vacation()
        ->halfDay()
        ->create();

    /** @var VacationBalance $balance */
    $balance = VacationBalance::factory()->create([
        'employee_id' => $request->employee_id,
        'year' => $request->start_date->year,
        'accrued' => 2500,
        'taken' => 50,
    ]);

    $action = app(RestoreVacationBalance::class);
    $action->handle($request);

    $balance->refresh();
    expect($balance->taken)->toBe(0);
});
