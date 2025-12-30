<?php

declare(strict_types=1);

use App\Data\PeopleDear\TimeOffType\TimeOffUnitData;
use App\Enums\TimeOffUnit;
use Illuminate\Support\Collection;

test('time off unit options return a collection of time off unit data', function (): void {

    $options = TimeOffUnit::options();

    expect($options)
        ->toBeInstanceOf(Collection::class)
        ->and($options->first())
        ->toBeInstanceOf(TimeOffUnitData::class);

});
