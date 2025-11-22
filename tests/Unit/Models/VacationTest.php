<?php

declare(strict_types=1);

use App\Models\VacationBalance;

test('calculates remaining vacations', function (): void {

    /** @var VacationBalance $vacationBalance */
    $vacationBalance = VacationBalance::factory()
        ->create([
            'from_last_year' => 5,
            'accrued' => 22,
            'taken' => 10,
        ]);

    expect($vacationBalance->remaining)
        ->toBe(17);

});
