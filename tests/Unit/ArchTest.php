<?php

declare(strict_types=1);

use App\Models\VacationBalance;

arch()->preset()->php();
arch()->preset()
    ->strict()
    ->ignoring(VacationBalance::class);
arch()->preset()->security()->ignoring([
    'assert',
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
