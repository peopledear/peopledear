<?php

declare(strict_types=1);

use App\Enums\BalanceType;
use Illuminate\Support\Collection;

test('options', function (): void {

    $options = BalanceType::options();

    expect($options)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(4);

});

test('labels', function (BalanceType $case, string $label): void {

    expect($case->label())->toBe($label);

})->with([
    [BalanceType::None, 'None'],
    [BalanceType::Annual, 'Annual'],
    [BalanceType::PerEvent, 'Per Event'],
    [BalanceType::Recurring, 'Recurring'],
]);
