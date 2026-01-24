<?php

declare(strict_types=1);

use App\Enums\TimeOffTypeStatus;

test('options', function (): void {

    $options = TimeOffTypeStatus::options();

    expect($options)
        ->toBeArray()
        ->toHaveCount(3)
        ->toBe([
            1 => 'Pending',
            2 => 'Active',
            3 => 'Inactive',
        ]);

});

test('labels', function (TimeOffTypeStatus $case, string $label): void {

    expect($case->label())->toBe($label);

})->with([
    [TimeOffTypeStatus::Pending, 'Pending'],
    [TimeOffTypeStatus::Active, 'Active'],
    [TimeOffTypeStatus::Inactive, 'Inactive'],
]);

test('colors', function (TimeOffTypeStatus $case, string $color): void {

    expect($case->color())->toBe($color);

})->with([
    [TimeOffTypeStatus::Pending, 'warning'],
    [TimeOffTypeStatus::Active, 'success'],
    [TimeOffTypeStatus::Inactive, 'secondary'],
]);
