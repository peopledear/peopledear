<?php

declare(strict_types=1);

use App\Data\PeopleDear\VacationBalance\VacationBalanceData;
use App\Models\VacationBalance;

test('creates with remaining', function (): void {

    /** @var VacationBalance $vacationBalance */
    $vacationBalance = VacationBalance::factory()
        ->create([
            'from_last_year' => 800,
            'accrued' => 2250,
            'taken' => 900,
        ])
        ->fresh();

    $data = VacationBalanceData::from($vacationBalance);

    expect($data->accrued)
        ->toBe('22.5')
        ->and($data->fromLastYear)
        ->toBe('8')
        ->and($data->taken)
        ->toBe('9')
        ->and($data->remaining)
        ->toBe('21.5')
        ->and($data->yearBalance)
        ->toBe('21.5')
        ->and($data->lastYearBalance)
        ->toBe('0');

});
