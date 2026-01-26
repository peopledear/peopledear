<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\VacationBalance;
use App\Support\Exceptions\DomainException;

arch()->preset()->php();
arch()->preset()
    ->strict()
    ->ignoring([
        VacationBalance::class,
        Notification::class,
        DomainException::class,
        'App\Filament\Resources',
    ]);
arch()->preset()->security()->ignoring([
    'assert',
    App\Providers\FortifyServiceProvider::class, // Uses sha1 to match Laravel's VerifyEmail behavior
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
