<?php

declare(strict_types=1);

use App\Enums\PeriodStatus;
use App\Models\Period;

test('period model has a status enum cast', function (): void {

    /** @var Period $period */
    $period = Period::factory()
        ->create()
        ->fresh();

    expect($period->status)
        ->toBeInstanceOf(PeriodStatus::class);
});

test('period model can be created', function (): void {

    $period = Period::factory()
        ->create();

    $now = now();

    expect($period)
        ->toBeInstanceOf(Period::class)
        ->and($period->year)
        ->toBe($now->year);
});

test('to array', function (): void {
    $period = Period::factory()
        ->create()
        ->fresh();

    expect(array_keys($period->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
            'organization_id',
            'year',
            'start',
            'end',
            'status',
        ]);

});
